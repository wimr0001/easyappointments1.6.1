insert into ea_settings (name, value) values("hide_customer_timezone", 1);
insert into ea_settings (name, value) values("services_for_new_customers", "[15,18,32]");

ALTER TABLE `ea_appointments` ADD `reminder_datetime` DATETIME NULL DEFAULT NULL AFTER `id_caldav_calendar`;
ALTER TABLE `ea_appointments` ADD `reminder_statustext` text NULL DEFAULT NULL AFTER `reminder_datetime`;
ALTER TABLE `ea_appointments` ADD `reminder_reasontext` text NULL DEFAULT NULL AFTER `reminder_statustext`;

ALTER TABLE `ea_roles` CHANGE `webhooks` `webhooks` INT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `ea_services` DROP `availabilities_type`;

Tested with 1.6.1, same problem exists in former versions
Situation:
The calendar is blocked for booking outside. The provider schedules appointments for existing customers in the backend, also in the block period (two months).

Now, the customer wants to reschedule the appointment. He is attended to the fact that he has to wait for almost two months. Nevertheless, he wants to reschedule.
Problem: the calendar of a blocked month is shown. None of the days are grayed-out.
Our customers are confused.
In my opinion, the calendar view must start with the first available month.

A technical remark:
Starting step 1: 4 http-request are sent.
After clicking "next" to step 2, the same http-request are again sent.
Isn't that a bit too much?