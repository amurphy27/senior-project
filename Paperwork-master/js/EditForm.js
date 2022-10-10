function dbUpdate(id, value, formID)
{
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
        {
            console.log("sent to db successfully");
        }
    }
    if (document.getElementById(id).type == "checkbox")
    {
        xhttp.open("POST", "dbSend.php");
        xhttp.send("id=" + id + "&value=" + document.getElementById(id).checked + "&formID=" +  formID);
    }
    else
    {
        xhttp.open("POST", "dbSend.php");
        xhttp.send("id=" + id + "&value=" + value+ "&formID=" + formID);
    }
}

function signButton(id, name, formID)
{
    button = document.getElementById(id);
    if (button.innerText == "Sign")
    {
        button.style.fontFamily = "Cedarville Cursive";
        button.style.fontSize = "70%";
        button.innerText = name;
    }
    else
    {
        button.style.fontFamily = "Arial";
        //button.style.fontSize = "1vw";
        button.style.fontSize = "100%";
        button.innerText = "Sign";
        name = "";
    }

    dbUpdate(id, name, formID);
}

function dbEmailUpdate(value, formID) 
{

    //Validate the emails, if invalid don't save to db.
    clearInvalidMsgs();

    if(emailsValid(value)){
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function()
        {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
            {
                console.log("sent emails to db successfully");
            }
        }

        xhttp.open("POST", "sendEmailsToDB.php");
        xhttp.send("&value=" + value+ "&formID=" + formID);

        //var msg = "<span class=\"invalidMsg\" id=\"invalidMsg\"> Emails saved! </p>";
        //document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
    }
    
}

function emailsValid(emailString){
    var emailArray = emailString.trim().split(/\s+/);

    if(emailArray.length == 0){
        //var msg = "<span class=\"invalidMsg\" id=\"message\"> Email chain must not be empty</p>";
        //document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
        return false;
    }

    var invalid = false;
    emailArray.forEach((email) => {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) == false)
        {   
            //change this to display html on the page by the email chain editor/input text box.
            var msg = "<p class=\"invalidMsg\"> " + email + " is an invalid email address! </p>";
            document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
            invalid = true;
        }

    });

    if(invalid){
        return false;
    }

    if(hasRepeatedEmail(emailArray)){
        var msg = "<span class=\"invalidMsg\" id=\"invalidMsg\"> No repeated emails allowed in the email chain. </p>";
        document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
        return false;
    }

    return true;
}

function hasRepeatedEmail(emailArray){
    var numRepeat = 0;

    for (let i = 0; i < emailArray.length; i++) {

        for (let j = 0; j < emailArray.length; j++) {
            
            if(emailArray[i] == emailArray[j]){
                numRepeat++;
            }
        }

        if(numRepeat >= 2){
            return true;
        }

        numRepeat = 0;
    }

}

function clearInvalidMsgs(){
    const msgs = document.getElementsByClassName("invalidMsg");
    while(msgs.length > 0){
        msgs[0].parentNode.removeChild(msgs[0]);
    }
}

function sendAsEmailChain(formID){

    var text = document.getElementById("emailChainInput").value;
    if(text === ''){
        var msg = " Email Cannot be Blank!";
        document.getElementById("message").innerHTML = msg;
    }
    else{
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function()

        {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
            {
                console.log("form sent via email chain");
            }
        }

        xhttp.open("POST", "BeginEmailChain.php");
        xhttp.send("formID=" + formID);

        var msg = "<span id=\"message\"<p> Form sent via email chain! </p><span>";
        document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
        
        document.getElementById("emailChainInput").readOnly = true;

        document.getElementById("sendAsEmailChain").remove();

    }
}

function sendAsMassEmail(formID){
    var text =document.getElementById("emailChainInput").value;
    if (text ===''){
        var msg = " Email Cannot be Blank!";
        document.getElementById("message").innerHTML = msg;
    }else{
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
        {
            console.log("form sent via Mass Email");
        }
    }

    xhttp.open("POST", "MassEmail.php");
    xhttp.send("formID=" + formID);

    var msg = "<span id=\"message\"<p> Form sent via Mass Email! </p><span>";
    document.getElementById("emailChainInput").insertAdjacentHTML("afterend", msg);
    
    document.getElementById("emailChainInput").readOnly = true;

    document.getElementById("sendAsMassEmail").remove();
    }
}


function approveForm(formID, email, action){
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
        {
            console.log("form action accepted");
        }
    }

    xhttp.open("POST", "EmailChain.php");
    xhttp.send("&formID=" + formID+ "&email=" + email+ "&action=" + action);

    if(action == "approve"){
        var msg = "Form approved.";
        document.getElementById("message").innerHTML = msg;

        //make all buttons disappear
        document.getElementById("approveForm").remove();
        document.getElementById("disapproveForm").remove();
        if(document.getElementById("editForm")){
            document.getElementById("editForm").remove();
        }

        //make all fields uneditable, not actually secure
        var form = document.getElementById("bigForm");

        var fieldsArray = form.querySelectorAll("input, button, :scope > div");

        for (let i = 0; i < fieldsArray.length; i++) {
            if(fieldsArray[i].type == "checkbox" || fieldsArray[i].type == "date" || fieldsArray[i].type == "button"){
                fieldsArray[i].setAttribute("onclick", "return false;");
            }
            else if(fieldsArray[i].className == "divText"){
                fieldsArray[i].setAttribute("contentEditable", false);
            }
            else if(fieldsArray[i].type == "text"){
                fieldsArray[i].readOnly=true;
            }
        }
    }
    else if(action == "disapprove"){
        var msg = "Form disapproved.";
        document.getElementById("message").innerHTML = msg;

        //make all buttons disappear
        document.getElementById("approveForm").remove();
        document.getElementById("disapproveForm").remove();
        if(document.getElementById("editForm")){
            document.getElementById("editForm").remove();
        }
        
        //make all fields uneditable, not actually secure
        var form = document.getElementById("bigForm");

        var fieldsArray = form.querySelectorAll("input, button, :scope > div");

        for (let i = 0; i < fieldsArray.length; i++) {
            if(fieldsArray[i].type == "checkbox" || fieldsArray[i].type == "date" || fieldsArray[i].type == "button"){
                fieldsArray[i].setAttribute("onclick", "return false;");
            }
            else if(fieldsArray[i].className == "divText"){
                fieldsArray[i].setAttribute("contentEditable", false);
            }
            else if(fieldsArray[i].type == "text"){
                fieldsArray[i].readOnly=true;
            }
        }
        
    }
    else if(action = "edit"){

        //open up the fields to be editable
        var form = document.getElementById("bigForm");
        //var fieldsArray = form.elements;
        var fieldsArray = form.querySelectorAll("input, :scope > div");

        for (let i = 0; i < fieldsArray.length; i++) {
            if(fieldsArray[i].type == "checkbox" || fieldsArray[i].type == "date"){
                fieldsArray[i].setAttribute("onclick", "return true;");
            }
            else if(fieldsArray[i].className == "divText"){
                fieldsArray[i].setAttribute("contentEditable", true);
            }
            else if(fieldsArray[i].type == "text"){
                fieldsArray[i].readOnly=false;
            }
            
        }

        //make edit button disappear
        document.getElementById("editForm").remove();

    }
}

function formTitle(formTitle, formID){

    if(!(formTitle.trim().length === 0)){
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function()
        {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
            {
                console.log("formTitle sent !");
            }
        }

        xhttp.open("POST", "formTitle.php");
        xhttp.send("formTitle=" + formTitle+ "&formID=" + formID);
    }
}

function comment(formID)
{
    var comments = document.getElementById("formComments").innerHTML;
    //console.log("Comments = " + comments);
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
        {
            console.log("formComment sent !");
        }
    }
    xhttp.open("POST", "formComment.php");
    xhttp.send("comments=" + comments+ "&formID=" + formID);
}