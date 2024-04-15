<p>Install WSL on Windows</p>

<p>Bitbucket Setup</p>

<p>
Generate SSH Key for repo access
<ol>
<li>Install WSL<br />
   - wsl --install</li>
<li>Open WSL command promt and run the following command</li>
<li>ssh-keygen -t rsa -b 2048<br />
   - Reference: https://support.atlassian.com/bitbucket-cloud/docs/configure-ssh-and-two-step-verification/</li>
<li>Goto github repo -> settings -> SSH and GPG keys -> New SSH key -> Copy and paste "cat ~/.ssh/id_rsa.pub" in WSL</li>
<li>git clone git@github.com:Negotium-Solutions/negotium_api.git</li>
<li>Install PHP 8.2 by running the following commands, so we can install sail via composer
	<ul>
        <li>sudo add-apt-repository ppa:ondrej/php</li>
	    <li>sudo apt update</li>
	    <li>sudo apt install php8.2 php8.2-curl php-mbstring php-xml php-zip php-gd php-json php-mysql php-common openssl -y</li>
    </ul>
</li>
<li>Install Laravel sail
	<ul><li>composer require laravel/sail --dev</li>
	<li>php artisan sail:install</li>
    </ul></li>
<li>Finally run sail up -d / vendor/bin/sail up -d (Google how to add this so you don't have to always run vendor/bin/sial up -d and you can just run sail up -d)</li>
<li>Run any command using sail (sail artisan migrate:fresh --seed)</li>
<li>Also write tests for every new functionality that follows the API / Vue way of doing things</li>
</ol>
<p style="font-weight: bold; font-size: large;">Migrations</p>
<p style="font-weight: bold;">Central App Migrations</p>
<p>See full documentation here: <a href="https://laravel.com/docs/10.x/migrations#main-content" target="_blank">https://laravel.com/docs/10.x/migrations#main-content</a></p>
<ol>
    <li>
        Run migrations
        <ul>
            <li>[sail/php] artisan migrate</li>
        </ul>
    </li>
    <li>
        Clear all databases
        <ul>
            <li>[sail/php] artisan migrate:fresh</li>
        </ul>
    </li>
    <li>
        Seed data
        <ul>
            <li>[sail/php] artisan migrate --seed</li>
        </ul>
    </li>
</ol>
<p style="font-weight: bold;">Tenant App Migrations</p>
<p>See full documentation here: <a href="https://tenancyforlaravel.com/docs/v3/introduction/" target="_blank">https://tenancyforlaravel.com/docs/v3/introduction/</a></p>
<ol>
    <li>
        Run migrations
        <ul>
            <li>[sail/php] artisan tenants:migrate</li>
        </ul>
    </li>
    <li>
        Clear all tenants databases
        <ul>
            <li>[sail/php] artisan tenants:migrate-fresh</li>
        </ul>
    </li>
    <li>
        Seed tenants data
        <ul>
            <li>[sail/php] artisan tenants:seed</li>
        </ul>
    </li>
</ol>

<p style="font-weight: bold;">Deploy the Swagger API</p>
<p>When working on the API, make sure you always update new endpoints with swagger documentation annotations as function comments, so the documentation is always up to date.</p>
<ul>
    <li>[sail/php] artisan l5-swagger:generate</li>
</ul>
