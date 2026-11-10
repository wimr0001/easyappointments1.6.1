<?php 

/*
use \EA\Engine\Types\Text;
use \EA\Engine\Types\Email;
use \EA\Engine\Types\Url; 
*/

include "../system/core/Controller.php";

class Reminder_Sender extends CI_Controller{
	
    /**
     * @var array
     */
    protected $privileges;

    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();

        // All the methods in this class must be accessible through a POST request.
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            $this->security->csrf_show_error();
        }

        $this->load->library('session');
        $this->load->model('roles_model');
        $this->privileges = $this->roles_model->get_privileges($this->session->userdata('role_slug'));

        // Set user's selected language.
        if ($this->session->userdata('language')) {
        	$this->config->set_item('language', $this->session->userdata('language'));
        	$this->lang->load('translations', $this->session->userdata('language'));
        } else {
        	$this->lang->load('translations', $this->config->item('language')); // default
        }
    }
	
	public function send(){
            // :: SEND EMAIL NOTIFICATIONS TO PROVIDER AND CUSTOMER
            try {
                $this->config->load('email'); 
                $email = new \EA\Engine\Notifications\Email($this, $this->config->config);

                $send_provider = $this->providers_model
                            ->get_setting('notifications', $provider['id']);

                if (!$manage_mode) {
                    $customer_title = new Text($this->lang->line('appointment_booked'));
                    $customer_message = new Text($this->lang->line('thank_you_for_appointment'));
                    $provider_title = new Text($this->lang->line('appointment_added_to_your_plan'));
                    $provider_message = new Text($this->lang->line('appointment_link_description'));
                } else {
                    $customer_title = new Text($this->lang->line('appointment_changes_saved'));
                    $customer_message = new Text('');
                    $provider_title = new Text($this->lang->line('appointment_details_changed'));
                    $provider_message = new Text('');
                }

                $customer_link = new Url(site_url('appointments/index/' . $appointment['hash']));
                $provider_link = new Url(site_url('backend/index/' . $appointment['hash']));

                $send_customer = $this->settings_model->get_setting('customer_notifications');

				if ((bool)$send_customer === TRUE) {
                    $email->sendAppointmentDetails($appointment, $provider,
                            $service, $customer, $company_settings, $customer_title,
                            $customer_message, $customer_link, new Email($customer['email']));
                }

                if ($send_provider == TRUE) {
                    $email->sendAppointmentDetails($appointment, $provider,
                            $service, $customer, $company_settings, $provider_title,
                            $provider_message, $provider_link, new Email($provider['email']));
                }
				
            } catch(Exception $exc) {
                $warnings[] = exceptionToJavaScript($exc);
            }
	}
	
}

$reminder = new Reminder_Sender();
