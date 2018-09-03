# project_hour_logger
Description:
Simple hour logger for tracking how many hours you put into your projects.  Full CRUD system in php/mysql.

Technologies used:
HTML, CSS, PHP, MySQL, JavaScript
(the bare minimum technologies used.  I wanted to keep it dead simple.)

Use case:
I needed something super simple to log how many hours I put into each of my programming projects.  The way I like to work is by setting a 1 hour timer during which time I'm 100% focused on the task at hand.  After each hour I use this hour logger to log the hour and I'll take a short break or whatever before starting up a new "focus hour."  As a freelancer, I want to keep track of how many total hours my projects take.  This will help me make time requirement estimates for future projects.

Setup:
1. Create a new database schema or use an existing one where the 'projects' table will reside.
2. Use your dbms or the 'projects.sql' file to create the projects table.
3. Follow the config steps at the top of 'p_ctr.php' to authorize your IP address and use your database credentials.
4. Put 'p_ctr.php' somewhere on your web server and navigate to it in your browser.
