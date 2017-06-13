<?php
/**
 *
 * @author          VoiceNation, LLC
 * @copyright       2015-2016, VoiceNation LLC
 * @link            http://www.voicenation.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.

 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.

 *   You should have received a copy of the GNU Affero General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
       
                <h2 class="header">Record Audio for <?php echo $account_number . ' - ' . $did_name; ?></h2>
			
                <div class="inner" style="height: 5em;">
                    <audio id="audio" autoplay controls></audio>
                    <button id="record-audio">Record</button>
                    <button id="stop-recording-audio" disabled>Stop</button>
                    <button id="save-recording-audio" disabled>Save</button>
                    <h2 id="audio-url-preview"></h2>
                </div>
            <script>
                function getByID(id) {
                    return document.getElementById(id);
                }

                var recordAudio = getByID('record-audio'),
                    stopRecordingAudio = getByID('stop-recording-audio'),
                    saveRecordingAudio = getByID('save-recording-audio');


                var canvasWidth_input = getByID('canvas-width-input'),
                    canvasHeight_input = getByID('canvas-height-input');

                var audio = getByID('audio');


                var audioConstraints = {
                    audio: true,
                    video: false
                };

            </script>            
            
            <script>
                var audioStream;
                var recorder;
                var blob;

                recordAudio.onclick = function() {
                    if (!audioStream)
                        navigator.getUserMedia(audioConstraints, function(stream) {
                            if (window.IsChrome) stream = new window.MediaStream(stream.getAudioTracks());
                            audioStream = stream;

                            audio.src = URL.createObjectURL(audioStream);
                            audio.muted = true;
                            audio.play();

                            // "audio" is a default type
                            recorder = window.RecordRTC(stream, {
                                type: 'audio'
                            });
                            recorder.startRecording();
                        }, function() {
                        });
                    else {
                        audio.src = URL.createObjectURL(audioStream);
                        audio.muted = true;
                        audio.play();
                        if (recorder) recorder.startRecording();
                    }

                    window.isAudio = true;

                    this.disabled = true;
                    stopRecordingAudio.disabled = false;
                };

                var screen_constraints;
                
                saveRecordingAudio.onclick = function() {
                    if (recorder) {
                            blob = recorder.getBlob();
                            
                          
                            var formData = new FormData();
//                            formData.append(fileType + '-filename', fileName);
                            formData.append('audio-blob', blob);
                            formData.append('did_id', '<?php echo $did_id; ?>');
                                                        
                            xhr('/DidNumbers/save_audio/<?php echo $did_id; ?>', formData, function (data) {
                              var jsondata = JSON.parse(data);
                              if (jsondata.success) {
                                loadPage(this, '/DidNumbers/edit/<?php echo $did_id; ?>', 'did-content');                                  
                                $('#record-did').dialog('close');
                              }
                              else {
                                alert(jsondata.msg);
                              }
                                //window.open(location.href + fName);
                            });                            
                                           
                    }
                };


                function xhr(url, data, callback) {
                    var request = new XMLHttpRequest();
                    request.onreadystatechange = function () {
                        if (request.readyState == 4 && request.status == 200) {
                          console.log(request.responseText);
                            callback(request.responseText);
                        }
                    };
                    request.open('POST', url);
                    request.send(data);
                }

                stopRecordingAudio.onclick = function() {
                    this.disabled = true;
                    recordAudio.disabled = false;
                    audio.src = '';

                    if (recorder)
                        recorder.stopRecording(function(url) {
                            saveRecordingAudio.disabled = false;
                            audio.src = url;
                            audio.muted = false;
                            audio.play();
                    });
                };


            </script>
      