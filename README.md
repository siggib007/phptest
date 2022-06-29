# phptest
## PHP Demo Overview and history
Wanted something quick and simple to verify that all the components where in place to make a PHP site driven by mySQL/MariaDB database so I put together this test site. Then this grew into a demo site to test various functions and features as well as base code for a fully functioning PHP site, except it is lacking all meaningful functionality as it is just a base to be built upon. It does however have all the base authentication, registration, setup and administration functionality. I plan to add more base features as I think of them and have time to implement them.

There are few environment variables needed for both the SQL server and the PHP part. I've tested this with environment variables as well as using Doppler Secrets management (see https://infosechelp.net/secrets-management/ for more info on them) as well as a secret management system from AKEYLESS systems (https://infosechelp.net/secrets-management-a-key-less-edition for more info on them). See DBConf.php for more details on environment variables required.

One thing to note, I'm not a front end designer. This site is very functional and utilitarian, however it will never win any awards or even compliments for aesthetics or anything along those lines. Just to set proper expectation. If you are an CSS expert and love the UI design aspects of web developments, knock yourself out and fix the layout. You are even welcome to submit a PR with your fixes if that floats your boat.

Also if you are deploying this anywhere other than your laptop for testing purposes I strongly recommend you delete EmailTest.php from the server. If an unauthorized person where to gain access to this site they could start sending emails in your name and bypassing DMARC/SPF. 

You might also want to remove the Archive folder as that just contains old junk. 

## Deploy with Docker and Doppler

To set this up using Docker and fetching secrets to Doppler run the following commands. This assumes Docker, docker-compose and Doppler CLI are already setup and that Doppler CLI is properly authenticated into your Doppler workspace.

Run the following commands from your terminal. FYI I'm doing this on a Windows 10 box and Docker Desktop for Windows. 

1. git clone https://github.com/siggib007/phptest.git phpdemo
2. doppler import
3. In DopplerVar.php line 2, change phpdev to phpdemo
4. doppler setup -c dev -p PHPDemo
5. doppler run -- docker-compose up -d

That should be it, you should be good to go now. Just open up a browser to http://localhost:88 and create yourself an account in this demo system.

## Deploy manually to a web server and a mySQL/mariadb server

If you would rather deploy this manually to PHP server and a mySQL or MariaDB server rather than use Docker here are the general steps you need to follow:

1. Execute DBCreatePopulate.sql against your database server, make sure you adjust the database create and use statement according to your preferred naming wishes. 
2. Deploy all the php and CSS files from this repo to your php enabled web server
3. Adjust DBConf.php according to how you are handling secrets and environmental varialbles. 
4. Create any required environment variables and make sure they are exposed to the PHP engine.
   I put them in httpd.conf during my testing using the format:
   `SetEnv DOPPLERKEY "topsecret key"`
5. If you want to use AKEYLESS system there is a shell script file aKeylessImport.sh that will create all the secrets needed, assuming you have their CLI installed. You would then adjust these values as necessary. I recommend against having password and API authentication keys in any sort of shell scripting or import file, rather manually update those on the CLI or in the GUI later.
