This project shows you how to use the Clock component, to configure it and to
react to the OnAlarm event.

The OnAlarm event is fired everytime the Alarm property happens, which in the
sample is set:

Date.parse(new Date)+5000

Which is javascript code to set the alarm at 5 seconds *after* the current date.

In the OnAlarm event, the alarm is set again, to make the component fire events
continuosly:

dt = Date.parse(dt)+5000
Clock1.fld.setAlarm(dt);
