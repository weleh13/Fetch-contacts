fetch_contacts
==============

Codeigniter library for fetching contacts from Google, Yahoo and Live accounts. 

This API directory includes 3 fetch contacts APIs (Google, Yahoo, Live). 

<h2>Installation and Usage</h2>
Create new projects with your developer accounts.

1. Google 
Go to https://cloud.google.com and create a new project. 
2. Yahoo
Go to http://developer.yahoo.com/ and create a new project. Please note that if you want set this up localy, you will need to create virtual host because yahoo doesn't allow you to use localhost for security reasons. You can find <a href="http://stackoverflow.com/questions/3623208/how-can-i-get-yahoo-oauth-to-work-when-i-develop-locally-when-my-local-domain-is">this</a> thread helpfull 
3. Live
Go to https://account.live.com/developers/applications and create a new project. 

Once everything is set up, open the contacts_api.php config file and update the values for your applications.

You can call contact fetching functions by hiting these urls in browser:
1. Google
baseurl().'welcome/getGoogleResponse';
2. Yahoo
baseurl().'welcome/connectYahoo';
3. Live
baseurl().'welcome/connectLive';

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/TsTrv/fetch_contacts/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

