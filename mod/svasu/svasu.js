window.onload = function() {
    //document.getElementById('id_general').style.display = 'none';
    document.getElementById('id_displaysettings').style.display = 'none';
    document.getElementById('id_availability').style.display = 'none';
    document.getElementById('id_gradesettings').style.display = 'none';
    document.getElementById('id_attemptsmanagementhdr').style.display = 'none';
    document.getElementById('id_compatibilitysettingshdr').style.display = 'none';
    document.getElementById('id_modstandardelshdr').style.display = 'none';
    document.getElementById('id_availabilityconditionsheader').style.display = 'none';
    document.getElementById('id_activitycompletionheader').style.display = 'none';
    document.getElementById('id_tagshdr').style.display = 'none';
    document.getElementById('id_competenciessection').style.display = 'none';
    var link = document.getElementsByClassName('fp-btn-add')[0].innerHTML;
    alert(link);
    link.click();
};
//open iframe
function openpopup(){
    //var token           = document.getElementsByName("configToken");
    //var param           = 'token='+ token[0].value+'&functionName=getScormPackageFromSvasu';
    //url                 = 'https://staging.svasu.cloud/svasu/login?'+param;     
    var newUrl          = 'https://staging.svasu.cloud/svasu/login?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL21vb2RsZS1zdmFzdVwvIiwidGhpcmRQYXJ0eVR5cGUiOiJtb29kbGUifQ.KHIo_r2iV_Ij91270hfOt7EBG7bb16FWyMat1UmAvNI&functionName=getScormPackageFromSvasu';
    var myiframe        = document.getElementById("myiframe");
    myiframe.innerHTML  = '<iframe id="svasuCloudFrame" class="myiframec"  src="' + newUrl + '" width="800" height="400"></iframe>';   
}

//this code resolve cors issue
if (window.addEventListener) {
    window.addEventListener("message", getScormPackageFromSvasu);
} else {
    window.attachEvent("onmessage", getScormPackageFromSvasu);
}
//cors end

//get response from svasu
async function getScormPackageFromSvasu(event){   
    //hide iframe
    document.getElementById('myiframe').style.display = 'none';
    //get scorm binary content
    if (event.origin != "https://staging.svasu.cloud") {
        console.log("The message came from some site we don't know. We're not processing it.");
        return;
    }
	var dataFromChildIframe = event.data;
    var binaryContent  = dataFromChildIframe.message;
    //convert binary content to zip content
    const blob = new Blob([binaryContent], { type: 'application/octet-stream' });
    const reader = new FileReader();
    reader.readAsArrayBuffer(blob);
    reader.onloadend = function() {
       console.log(reader.result); // Outputs the contents of the blob as an ArrayBuffer
    }
    const url = URL.createObjectURL(blob);
    console.log(url);
    var filename = 'sco.zip';

    //const zipGenerator = new zip.Generator({comment: 'Zip file created with zip.js'});

    // Add the Blob to the ZIP file with a specified file name
    //zipGenerator.add('file.bin', new Uint8Array(await blob.arrayBuffer()));

    // Generate the ZIP file as a Blob
   // const zipContent = new Blob([await zipGenerator.generateAsync()], {type: 'application/zip'});

    // You can now use the 'zipContent' variable to access the ZIP file content
   // console.log(zipContent);

    //download scorm zip
    /*
    var blobData = (typeof bom !== 'undefined') ? [bom, binaryContent] : [binaryContent];
    var blob = new Blob(blobData, {type: 'application/octet-stream'});
    if (typeof window.navigator.msSaveBlob !== 'undefined') {
        window.navigator.msSaveBlob(blob, filename);
    } else {
        var blobURL = (window.URL && window.URL.createObjectURL) ? window.URL.createObjectURL(blob) : window.webkitURL.createObjectURL(blob);
        var tempLink = document.createElement('a');
        tempLink.style.display = 'none';
        tempLink.href = blobURL;
        tempLink.setAttribute('download', filename);
        if (typeof tempLink.download === 'undefined') {
            tempLink.setAttribute('target', '_blank');
        }
        document.body.appendChild(tempLink);
        tempLink.click();
        // Fixes "webkit blob resource error 1"
        setTimeout(function() {
            document.body.removeChild(tempLink);
            window.URL.revokeObjectURL(blobURL);
        }, 200)
    }*/

    //set value of mandatory field
    //$('#id_name').val(filename);
    
    //upload zip file
    //var params = {};
    //params['title'] = 'C:/Users/Saumya/Downloads/ks.zip';
    //params['maxbytes'] = 9999999999999999999;
    var nos = document.getElementsByTagName("noscript")[1].innerHTML;
    var noi = nos.replace("<div><object type='text/html' data='https://localhost/moodle/repository/draftfiles_manager.php?",'');
    var newnoi = noi.replace("' height='160' width='600' style='border:1px solid #000'></object></div>",'');
    //alert(newnoi);
    var newDatas = newnoi.replaceAll("&amp;","&");
    var serializeArray = newDatas.replaceAll("action=browse","action=upload");
    //alert(serializeArray);
    //var myArray = params.split("&amp;");
    //alert(myArray);    
    //var dataString = $("#repo-form_63aab3fca9863").serialize();

    /*$.ajax({
        url: '/moodle38/webservicesupload.php',
        type: 'post',
        data : {
            'filecontent' : '',
        },
        success: function(response){
            console.log(response);
            if(response != 0){
               alert('file uploaded');
            }
            else{
                alert('file not uploaded');
            }
        },
    });*/

    //submit form automatically
    
    //hide iframe
}