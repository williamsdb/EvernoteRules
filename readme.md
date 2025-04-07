<a name="readme-top"></a>


<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">Evernote Rules</h3>

  <p align="center">
    Move, tag, and manipulate Evernote notes automatically.
    <br />
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

I have been an avid user of Evernote since July 2008 and have amassed over 53,000 notes. Over those 16 years, I have seen many changes to both Evernote the company and the client itself. The recent purchase by Bending Spoons had the potential to make or break the company but has, in my opinion, been overwhelmingly positive for Evernote (despite what the naysayers on Reddit say!).

One feature I have wanted Evernote to have is some form of automation that manipulates notes based on rules - very similar to the rules [available in Outlook](https://support.microsoft.com/en-gb/office/manage-email-messages-by-using-rules-c24f5dea-9465-4df4-ad17-a50704d66c59). After 16 years of waiting for Evernote to implement this, I got fed up with waiting and wrote it myself. 

While I have tried to make it very simple it still requires a bit of setup and hosting by you. As part of the simplicity it has no database, few external libraries (simple.css, smarty, and, of course, the Evernote SDK amongst others), and crucially, has no security, I recommend that you use [Cloudflare Zero Trust](https://www.spokenlikeageek.com/2024/04/09/cloudflare-zero-trust/) to secure it. If you can live with all of that read on.

One final word of warning - this comes with absolutely no warranty whatsoever. Here be dragons!

<a href='https://ko-fi.com/Y8Y0POEES' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://storage.ko-fi.com/cdn/kofi5.png?v=6' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/07/2024-07-24-08-09-24.png)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [PHP](https://php.net)
* [evernote-cloud-sdk-php](https://github.com/Evernote/evernote-cloud-sdk-php)
* [simple.css](https://simplecss.org/)
* [smarty](https://github.com/smarty-php/smarty)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

### Keys from Evernote

As implied above there are a few hoops that you have to jump through to get this working including relying on Evernote to provide you with some necessary access.

The code uses the Evernote SDK for PHP to manipulate your Evernote notes and to access that you need to [request an API Key](https://dev.evernote.com/doc). You will returned within a few days both a key and secret, which you’ll use below.

When thinking about implementing the functionality I could have periodically scanned a notebook looking for changes but this would have been hugely inefficient and Evernote asks you not to do this. Instead, you are asked to use [webhooks](https://dev.evernote.com/doc/articles/polling_notification.php#webhooks) which whenever there are any changes in a specified notebook Evernote sends a notification to a URL you provide.

Register your webhook by [following the instructions here](https://dev.evernote.com/support/faq.php#activatehook). You will need to provide Evernote with your API Key obtained above, the notebook(s) that you want to monitor and the URL the webhooks should be forwarded to. This will be as follows:

    https://<your domain>/webhook

The Evernote FAQ on webhooks suggests that “your Webhook will be activated, typically within 5 business days” – I found that it was a lot longer than that so be prepared for a wait.

### Hosting the app

As I said at the beginning this requires your own API and webhook details you also have to host it yourself. As the webhook needs to accessible on the public internet you’ll need it to be hosted on a public webserver somewhere. It is written in PHP, tested on version 8.1.13, requires no database and has been tested on Linux running Apache. Other configurations should work but may need some adjustment.

### Evernote SDK

If you take a look at the [Evernote SDK](https://github.com/Evernote/evernote-cloud-sdk-php) you will notice that it hasn’t been updated in a very long time. The most recent commit was five years ago and most of it is nine years old. A lot has happened in that time – versions of PHP have been released, Evernote has moved from the legacy client to V10, there’s a new note database and, of course, the company has been bought.

I can understand why Bending Spoons have prioritised the client over the SDK but not making any changes to it means that you will run into issues such as this:

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/07/GSx0fGYWQAAr2O6.png)

Fortunately, some kindly soul has posted a fix to this particular issue which [you can find here](https://github.com/Evernote/evernote-cloud-sdk-php/issues/45). You MUST patch the Evernote SDK with this fix before Evernote Rules will work.

It is, of course, entirely possible that you will run into other issues that I didn’t encounter. If you do let me know by [raising an issue here](https://github.com/williamsdb/EvernoteRules/issues) and I will endeavour to fix them.

### Prerequisites

Requirements are very simple, it requires the following:

1. PHP (I tested on v8.1.13)
2. [composer](https://getcomposer.org/)

### Installation


Here are some basic instructions to help you get up-and running:

1. download the code/clone the repository:

    > git clone https://github.com/williamsdb/EvernoteRules
    
2. install [composer](https://getcomposer.org/)
3. add the Evernote SDK (evernote-cloud-sdk-php) and smarty templating engine

    > composer.phar require evernote/evernote-cloud-sdk-php
    
    > composer.phar require smarty/smarty

4. update the SDK using the [details here](https://github.com/Evernote/evernote-cloud-sdk-php/issues/45)
5. create a cache folder for the Smarty templates (templates_c) and give the web server process to write to it
6. rename config_dummy.php to config.php and give the web server process to write to it 
7. create two empty files: rules.db, logs.db and give them appropriate permissions

On my LAMP server I achieve this as follows:


```console
php composer.phar require smarty/smarty
php composer.phar require evernote/evernote-cloud-sdk-php
sudo mkdir templates_c
sudo chown apache:apache templates_c -R
sudo chcon -R -t httpd_sys_rw_content_t templates_c
sudo mv config_dummy.php config.php
sudo touch rules.db
sudo touch logs.db
sudo chown apache:apache *.db
sudo chown apache:apache *.log
sudo chcon -R -t httpd_sys_rw_content_t *.db
sudo chown apache:apache config.php
sudo chcon -R -t httpd_sys_rw_content_t config.php
```

### Set-up

Now that you have the app installed you can configure it. Change the settings in config.php as necessary but leave the OAUTH blank as it will be completed for you when you connect to Evernote.

````php	
<?php
 
    // Callback URL for OAuth (no trailing slash)
    define("CALLBACK_URL", "https://yourdomin.com");
 
    // Your Evernote api keys
    define("KEY", "<your api comsumer key>");
    define("SECRET", "<your api consumer secret>");
 
    // Pushover keys - leave blank if not using
    define("PUSHOVER_TOKEN", "");
    define("PUSHOVER_USER", "");    
 
    // Log calls
    define("DEBUG", TRUE);
 
    // Your oAuth token - do not enter anything here!
    define("OAUTH","");
 
?>
````

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

### Authorising

The first time you load Evernote Rules you will see the following page allowing you to give it access to your Evernote account. Click the "Connect Evernote" button to get started.

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/08/2024-08-10-13-01-21.png)

You will be taken to Evernote to authorise the access. You will see the (probably) familiar page below. Note that Pigeonhole is what I called the app when I applied for my API keys so your page will display whatever you called it. Click Authorize to grant access.

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/07/2024-07-18-08-40-40.png)

If however, you see the following Evernote error instead:

    Missing required oauth parameter "oauth_token"

Then there is a possibility that the Evernote SDK is returning a 403 Forbidden code. It is not obvious from the output and I had to add tracing to the SDK to discover this. To sort this I needed to contact Evernote dev support to get them to unblock my IP address.

When you load up Evernote Rules, if you have authorised Evernote, it will try and get your list of Notebooks and cache these so it will take a few seconds to load. If you find later that Notebooks are missing then try clearing the cache - see the debug section below.

Evernote Rules is now configured and you can begin to create the rules and actions that will be carried out.

### Rules

Every time a note is created or updated in a notebook that is being monitored and you have registered for webhooks, Evernote Rules will get notified. This note will then be checked against the rules that you have created and if they match the actions you created will be applied.

When you click the Add a new rule button you will be taken to the following page where you can choose the rules. 

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/08/2024-08-10-16-03-21.png)

Give your rule a name and then choose when you want the rule to apply: when the Note is created, updated or either. 

Next, choose what Notebook you want this to apply to. Here you will see a list of all your Notebooks but keep in mind that **Evernote Rules will only receive a notification for Notebooks you have registered for webooks**.

Next, you can check whether the Title contains a piece of text and where that has to appear.

The Author in the majority of cases with be your name and not much use but if you email in notes then the Author will be the sender of the email. For example, I automatically email in receipts from places such as Amazon. In this case, the author has more useful information which you can use in a rule. I use this to then automatically move the note to a Receipts Notebook.

![](https://www.spokenlikeageek.com/wp-content/uploads/2024/08/2024-08-10-16-10-21.png)

Finally, you can match on any Tags that the note might have. Obviously, this is more likely to apply to updated notes than newly created ones. The Tags entered here must be comma separated and are case sensitive so Neil is different to neil.

Remember that ALL of these rules need to match for the actions to be executed.

### Actions

If all your rules have matched then the actions that you defined will be applied. With the exception of Delete the Note It doesn’t matter what order you apply the actions.

#### Move to Notebook

Will move the note from its current notebook to a new one. If you delete the notebook after creating the action then the action will silently fail and if you have logging turned on you will get notification there.

#### Change the Title

This is a simple string find and replace so you could, for example, remove “FW: ” from notes that have been emailed in. If you want to make multiple changes then create separate actions for each one.

If this proves too restrictive then I might look at implementing something grep based in future.

#### Add Tags

This is probably the most interesting action as it allows not only static tags to be added, for example: “Amazon”, “Neil” or “Invoice” but also there are a series of dynamic tags as follows:

| **Tag**  | **Description**                                                     |
|----------|---------------------------------------------------------------------|
| {year}   | Adds the current year in the format YYYY (2004, 2024 etc.)          |
| {month}  | The current month name in full (January, February etc.)             |
| {day}    | The day number (1-31)                                               |
| {dow}    | The current day of the week in full (Monday, Tuesday etc.)          |
| {date}   | The full date in YYYY-MM-DD format (2024-08-13)                     |
| {dayord} | The same as {day} only with the appropriate ordinal (1st, 2nd etc.) |

To use this action create a comma separated list of tags that you want adding, for example:

    Amazon,Receipt,{year},{month}

#### Delete the Note

Fairly obviously this deletes the note.

#### Send Notifications to Pushover

This is the only action that has no effect on your note. Instead this sends a notification to you via the [Pushover](https://pushover.net/) service when a rule has been triggered. This will come through with the following text:

    Rule <your rule name> has just been triggered

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/03/incoming-61D10C9F-0F22-4B56-8BC8-FB6A67172AF6.png)

If you are wondering why Pushover was chosen and not, say, email or text then that’s because I could implement it without any additional libraries. To send emails reliably, for example, you really need to use something like PHPMailer with an SMTP server which requires more setup.

_For more information, please refer to the [this blog post](https://www.spokenlikeageek.com/2025/04/01/evernote-rules/)_

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ROADMAP -->
## Known Issues

Because of the newness of Evernote Rules there is quite a bit of logging included which you can turn on by setting DEBUG in the config file. Setting it to 1 or TRUE will give you basic information and setting it to 2 will give you lots of detail. See this [blog post](https://www.spokenlikeageek.com/2025/04/01/evernote-rules/) for more information on this and viewing the logs.

The other quirk is in how Evernote now works. It used to be the case that you'd only get webhooks when you moved away from creating or editing a note. Now you get a webhook multiple times, even every keystroke.

### Updating a note gives: Attempt updateNote where RTE room has already been open for note

If you have the note open in the Evernote client that you want Evernote Rules to update then you will receive this message in the debug log ````Attempt updateNote where RTE room has already been open for note````. You can only have one process editing the note at once and effectively the Evernote client has it locked for updates.

See the [open issues](https://github.com/williamsdb/EvernoteRules/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- LICENSE -->
## License

Distributed under the GNU General Public License v3.0. See `LICENSE` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Bluesky - [@spokenlikeageek.com](https://bsky.app/profile/spokenlikeageek.com)

Mastodon - [@spokenlikeageek](https://techhub.social/@spokenlikeageek)

X - [@spokenlikeageek](https://x.com/spokenlikeageek) 

Website - [Contact](https://www.spokenlikeageek.com/contact/)

Project Link: [https://spokenlikeageek.com](https://www.spokenlikeageek.com/tag/EvernoteRules)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* None

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/github_username/repo_name.svg?style=for-the-badge
[contributors-url]: https://github.com/github_username/repo_name/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/github_username/repo_name.svg?style=for-the-badge
[forks-url]: https://github.com/github_username/repo_name/network/members
[stars-shield]: https://img.shields.io/github/stars/github_username/repo_name.svg?style=for-the-badge
[stars-url]: https://github.com/github_username/repo_name/stargazers
[issues-shield]: https://img.shields.io/github/issues/github_username/repo_name.svg?style=for-the-badge
[issues-url]: https://github.com/github_username/repo_name/issues
[license-shield]: https://img.shields.io/github/license/github_username/repo_name.svg?style=for-the-badge
[license-url]: https://github.com/github_username/repo_name/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/linkedin_username
[product-screenshot]: images/screenshot.png
[Next.js]: https://img.shields.io/badge/next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white
[Next-url]: https://nextjs.org/
[React.js]: https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB
[React-url]: https://reactjs.org/
[Vue.js]: https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D
[Vue-url]: https://vuejs.org/
[Angular.io]: https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white
[Angular-url]: https://angular.io/
[Svelte.dev]: https://img.shields.io/badge/Svelte-4A4A55?style=for-the-badge&logo=svelte&logoColor=FF3E00
[Svelte-url]: https://svelte.dev/
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
