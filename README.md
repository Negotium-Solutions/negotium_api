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
<li>Goto bitbucket repo -> Repository settings -> Access Keys -> Add Key -> Copy and paste "cat ~/.ssh/id_rsa.pub | pbcopy" in WSL
    <ol>
        <li>Install xcopy to be able to use pbcopy command "sudo apt-get install -y xclip"</li>
        <li>Open .bashrc and add an alias -> alias pbcopy="xclip -sel clip"</li>
        <li>Reference: https://coderwall.com/p/oaaqwq/pbcopy-on-ubuntu-linux</li>
    </ol>
</li>
<li>git clone git@bitbucket.org:blackboardbs/flow-laravel-10.8.git</li>
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