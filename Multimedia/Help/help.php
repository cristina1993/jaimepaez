<script type="text/javascript">
$('#uploader_div').ajaxupload({
        //OPTIONS A-Z
        allowExt:       [],                         //array of allowed upload extesion, can be set also in php script
        allowDelete:    false,                      //if enabled allow user to delete file after upload NOTE: should also be enabled from server side for security reason
        autoStart:      false,                      //if true upload will start immediately after drop of files or select of files
        async:          true,                       //set asyncron upload or not
         
        bandwidthUpdateInterval: 500,               //time interval in ms to refresh the bandwidth speed, false disable
        bootstrap:      false,                      //tell if to use bootstrap for theming buttons
         
        checkFileExists:false,                      //false=> do not ask user for file exits, true=> ask user to override or not the file
        chunkSize:      1048576,                    //default 1Mb,  //if supported send file to server by chunks, not at once
         
        data:           '',                         //other user data to send in GET to the php script
        dropColor:      'red',                      //back color of drag & drop area, hex or rgb
        dropClass:      'ax-drop',                  //class to add to the drop area when dropping files
        dropArea:       'self',                     //set the id or element of area where to drop files. default self
         
        enable:         true,                       //start plugin enable or disabled
        editFilename:   false,                      //if true allow edit file names before upload, by dblclick
        exifRead:       false,                      //enable exif read from jpeg
        exifWorker:     'js/exif-reader.js',        //if provided exif will be calculated by a worker, if not will be read as normal js exec
         
        flash:          'uploader.swf',             //flash uploader url for not html5 browsers
        form:           null,                       //integration with some form, set the form selector or object, and upload will start on form submit
         
        hideUploadButton:   true,                       //hide upload button on form integration, upload starts on form submit
         
        language:       'auto',                         //set regional language, default is english, avaiables: sq_AL, it_IT
        
        maxFiles:       9999,                       //max number of files can be selected
        maxConnections: 3,                          //max parallel connection on multiupload recomended 3, firefox support 6, only for browsers that support file api
        maxFileSize:    '10M',                      //max file size of single file,
        md5Calculate:   false,                      //calculate the md5 of file and send it to the server, CAN HANG BROWSER ON BIG FILES
        md5WorkerPath:  'js/file-md5.js',           //the path of md5 worker that makes the heavy calculation of md5
        md5CalculateOn: 'upload',                   //set when to calculate the md5: upload->calculate before upload starts, 'select'->calculate after selecting the file
         
        overrideFile:   false,                      //false=> do not ovveride on server side, true==> over, function let user decide
         
        thumbHeight:    0,                          //max thumbnial height if set generate thumbnial of images on server side
        thumbWidth:     0,                          //max thumbnial width if set generate thumbnial of images on server side
        thumbPostfix:   '_thumb',                   //set the post fix of generated thumbs, default filename_thumb.ext,
        thumbPath:      '',                         //set the path where thumbs should be saved, if empty path setted as remotePath
        thumbFormat:    '',                         //default same as image, set thumb output format, jpg, png, gif
         
        url:            'upload.php',               //php/asp/jsp upload script
        uploadDir:      false,                      //experimental feature, works on google chrome, for upload an entire folder content
         
          
        removeOnSuccess:false,                      //if true remove the file from the list after has been uploaded successfully
        removeOnError:  false,                      //if true remove the file from the list if it has errors during upload
        remotePath :    'uploads/',                 //remote upload path, can be set also in the php upload script
 
        resizeImage:{                               //resize images client side before upload
            maxWidth:   0,                              //max width of the new image
            maxHeight:  0,                              //max height of the new image
            quality:    0.5,                            //quality 
            scaleMethod:undefined,                      //custom scale method, internal scale method is based on bilinear scale
            format:     undefined,                      //format of output resized image, default takes the image format: possible values png/jpg
            removeExif: false                       // remove exif on resize before send to server
        },
         
        previews:       true,                       //disable previews of images . to avoid memory problem with browsers
         
        //CALLBACKS
        onStart:        function(fn){},             //event that runs on single file upload start
        onPreview:      function(){},               // this method is trigger when a file is rendered/scaled valid only for web images types
        onInit:         function(AU){},             //function that trigger on uploader initialization. Usefull if need to hide any button before uploader set up, without using css
        onSelect:       function(files){},          //function that trigger after a file select has been made, paramter total files in the queue
        beforeUpload:   function(filename, file){return true;},         //this function runs before upload start for each file, if return false the upload does not start
        beforeUploadAll:function(files){return true;},                  //this function runs before upload all start, can be good for validation
        onProgress:     function(bytes){},          //triggers on progress of upload, return total uploaded bytes (for all files)
         
        success:        function(fn){},             //function that triggers every time a file is uploaded
        finish:         function(fn, file){},       //function that triggers when all files are uploaded
        error:          function(err, fn){},        //function that triggers if an error occuors during upload,
         
        beforeSubmit:   function(fn, file, formsubmitcall){
            formsubmitcall.call(this);
        },              //event that runs before submiting a form
 
 
         
        validateFile:   function(name, extension, size){},              //user define function to validate a file, must return a string with error
        onBeforeChunkUpload: function(xhr, file, name, size, chunk){},  //this function is trigger before upload chunk starts
        onAfterChunkUpload: function(xhr, file, name, size, chunk){},   ///this functino is trigger after upload chunks starts
        onMd5Calculate: function(md5){},                                // this function is trigger after the md5 gets calculated
        onExifRead:     function(exif){},                                   // this function is trigger after the exif get read
        fileInfo: function(oData)
        {
            var strPretty = '';
            for (var a in oData) {
                if (oData.hasOwnProperty(a)) {
                    if (typeof oData[a] == 'object') {
                        strPretty += a + ' : [' + oData[a].length + ' values]<br>';
                    } else {
                        strPretty += a + ' : ' + oData[a] + '<br>';
                    }
                }
            }
            alert(strPretty);
        }                                           //function that get bind on info button of file, return file exif data (for images)
});
</script>         