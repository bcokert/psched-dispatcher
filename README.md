psched-dispatcher
===========
The message queue/dispatcher for my php message scheduler project


## Description
This is the dispatcher server, which is normally only used by a main server. It has two primary functions: storing scheduled messages to be posted at a later time, and posting said scheduled messages. It has a simple user interface that just displays all scheduled messages.

The main server posts messages and asks the dispatcher to schedule them. The dispatcher schedules message, and has a cron process that checks the database of messages and posts any that are ready to go out. Timing is only guaranteed to the minute, so scheduling down to the second may or may not be accurate.

When a scheduled message is posted, it is removed from the scheduling database. This helps the cron process, as it (some-what inefficiently) searches the whole database for messages that are ready every X seconds. To scale this, we could have a buffer working as follows:
- When a message is scheduled via scheduleMessage.php, if it is to be posted within 1 hour, it is added to the buffer, and NOT the sheduled message table
- One cron job runs every hour, checking the scheduled message table for messages to be posted within 1 hour, and adding them to the buffer (removing them from the scheduled messages table)
- Another cron job runs every X seconds, running through only the buffer.

This could be scaled further by having multiple buffers of different lengths, and having parallel buffers for smaller lengths

## Backend Interface By Example
This will be called by cron, and searches for any messages ready to be posted, then posts them:

`curl localhost/cron/checkScheduledMessages.php`

This will be called by the main server to schedule a message:

`curl --data "username=Curl&message=I'm scheduling a message!&time=2014-08-01 13:30:00" localhost/scheduleMessage.php`

