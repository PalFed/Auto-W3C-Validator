/*
    Place this file at the end of your HTML, just before </body>

    w3c_validate(doctype) creates a div with id 'w3c-status-outer-wrapper' for the results of the validation and then sends the HTML of the page
    to the validator to be checked.

    Upon completion, the HTML returned by the validator along with the close link are put into #w3c-status-outer-wrapper
 */
function w3c_validate(doctype, callbackfn)
{
    var div=document.getElementById("w3c-status-outer-wrapper");
    var close='<a href="javascript:void(0)" class="close-w3c-validator" onclick="w3c_close()">x</a>';
    if (div==null)
    {
        /* This is the first instantiation. Create the outer wrapper */
        div=document.createElement("div");
        div.setAttribute("id", "w3c-status-outer-wrapper");
        document.body.appendChild(div);
    }

    div.innerHTML=close+"<div class='loading w3c-status-loading'>Please wait, the HTML is being verified ... <div class=\"lds-ring\"><div></div><div></div><div></div><div></div></div></div>";

    xhr = new XMLHttpRequest();
    xhr.open('POST', 'src/validator.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {

        var html="";
        if (xhr.status === 200 && xhr.responseText!="") {
            html=close+xhr.responseText;
        }
        else  {
            html=close+"<div class='w3c-status-wrapper w3c-status-http-error'>An error occurred connecting to the validation service. <a href=\"javascript:void(0)\" onclick=\"w3c_validate('<!DOCTYPE html>')\" class=\"w3c-recheck-validation\">Re-check</a></div>";
        }
        div.innerHTML=html;
        if (typeof callbackfn === 'function') callbackfn();
    };
    xhr.onerror = function() {
        html=close+"<div class='w3c-status-wrapper w3c-status-http-error'>An error occurred connecting to the validation service. <a href=\"javascript:void(0)\" onclick=\"w3c_validate('<!DOCTYPE html>')\" class=\"w3c-recheck-validation\">Re-check</a></div>";
        div.innerHTML=html;
        if (typeof callbackfn === 'function') callbackfn();
    };

    xhr.send(encodeURI('doctype=' + doctype + '&html=' +document.documentElement.outerHTML));
}

function w3c_close()
{
    document.body.removeChild(document.getElementById("w3c-status-outer-wrapper"));
}
w3c_validate("<!DOCTYPE html>");