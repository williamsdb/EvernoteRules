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

While I have tried to make it very simple it still requires a bit of setup and hosting by you. As part of the simplicity it has no database, few external libraries (simple.css, smarty, and, of course, the Evernote SDK), just one Javascript function (no JQuery) and, crucially, has no security, I recommend that you use [Cloudflare Zero Trust](https://www.spokenlikeageek.com/2024/04/09/cloudflare-zero-trust/) to secure it. If you can live with all of that read on.

One final word of warning - this comes with absolutely no warranty whatsoever. Here be dragons!

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [PHP](https://php.net)
* [evernote-cloud-sdk-php] (https://github.com/Evernote/evernote-cloud-sdk-php)
* [simple.css](https://simplecss.org/)
* [smarty](https://github.com/smarty-php/smarty)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

Running the script is very straightforward:

1. download the code/clone the repository
2. install [composer](https://getcomposer.org/)
3. add the Evernote SDK (evernote-cloud-sdk-php) and smarty templating engine

    > composer.phar require evernote/evernote-cloud-sdk-php
    
    > composer.phar require smarty/smarty

4. update the SDK using the [details here](https://github.com/Evernote/evernote-cloud-sdk-php/issues/45)

You can read more about how it all works in [this blog post](https://www.spokenlikeageek.com/2024/07/12/computer-education-in-schools-instruction-language-cesil/).

### Prerequisites

Requirements are very simple, it requires the following:

1. PHP (I tested on v8.1.13)
2. [composer] (https://getcomposer.org/)

### Installation

1. Clone the repo:
   ```sh
   git clone https://github.com/williamsdb/EvernoteRules
   ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

_For more information, please refer to the [this blog post](https://www.spokenlikeageek.com/2024/07/12/computer-education-in-schools-instruction-language-cesil/)_

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ROADMAP -->
## Known Issues

- None

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

Your Name - [@spokenlikeageek](https://twitter.com/spokenlikeageek) - [Contact](https://www.spokenlikeageek.com/contact/)

Project Link: [https://spokenlikeageek.com] (https://www.spokenlikeageek.com/2023/11/06/posting-to-bluesky-via-the-api-from-php-part-one/)

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
