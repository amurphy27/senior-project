function confirmDelete(formID){
    var result = confirm("Are you sure you want to delete this draft?");
    if (result) {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function()
        {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200)
            {
                console.log("deleteButton was clicked");
                location.reload();
            }
        }
        xhttp.open("POST", "deleteDraft.php");
        xhttp.send("formID=" + formID);

    }
}