**Auto W3C Validator**

I built this validator while I was building PalFed to make sure that all of the pages I 
was generating with php and updating via ajax contained valid markup. This Validator utilises The 
[W3C Markup Validation Service](https://validator.w3.org/) combined with the [validator.nu HTML5 validator](https://html5.validator.nu/) to provide a fast and easy way to ensure that your web apps produce valid HTML5 as you develop them. 

In order to use it, you will need to install a local copy of the W3C Markup Validator. 
There are instructions on how to do this in many places on the web, you can also 
[use the instructions that I wrote](https://www.palfed.com/tech-tips/html5-validator) while I was creating the tool.

Once you have the W3C validator installed locally, copy the `src/validator.config.sample.php` file to `src/validator.config.php` and
update it with the details for your installation. You will need all the files in the src folder to make it work. The files 
in the root folder are for the demo page only.

On any page that you would like checked, simply include `src/validator.css` in your (development) CSS and add

    <script src="src/validator.js"></script> 
    
at the bottom of the page.

There is a hosted example of this at [https://validator.palfed.com/](https://validator.palfed.com/) so you can see
it in action, however it is on a low power dev server, so if you get connection errors, just click re-check. For my 
development, I am running on a local installation with validator and my development site both on a Virtualbox VM on my 
laptop and I have never had connection errors.