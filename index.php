<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="referrer" content="no-referrer" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no" />
    <title>Speedtest</title>

    <link href="bootstrap.min.css" rel="stylesheet" />
    <style type="text/css">
        .st-block {
            text-align: center;
        }
        .st-btn {
            margin-top: -0.5rem;
            margin-left: 1.5rem;
        }
        .st-value>span:empty::before {
            content: "0.00";
            color: #636c72;
        }
        #st-ip:empty::before {
            content: "___.___.___.___";
            color: #636c72;
        }
    </style>
</head>
<body class="my-4">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 mb-3">
                <p class="h1">
                    Speedtest
                    <button id="st-start" class="btn btn-outline-primary st-btn" onclick="startTest()">Start</button>
                    <button id="st-stop" class="btn btn-danger st-btn" onclick="stopTest()" hidden="true">Stop</button>
                </p>
                <p class="lead">
                    Your IP: <span id="st-ip"></span>
                </p>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 st-block">
                <h3>Download</h3>
                <p class="display-4 st-value"><span id="st-download"></span></p>
                <p class="lead">Mbit/s</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 st-block">
                <h3>Upload</h3>
                <p class="display-4 st-value"><span id="st-upload"></span></p>
                <p class="lead">Mbit/s</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 st-block">
                <h3>Ping</h3>
                <p class="display-4 st-value"><span id="st-ping"></span></p>
                <p class="lead">ms</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 st-block">
                <h3>Jitter</h3>
                <p class="display-4 st-value"><span id="st-jitter"></span></p>
                <p class="lead">ms</p>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var worker = null
        function startTest() {
            document.getElementById('st-start').hidden = true
            document.getElementById('st-stop').hidden = false
            worker = new Worker('speedtest_worker.min.js')
            var interval = setInterval(function () { worker.postMessage('status') }, 100)
            worker.onmessage = function (event) {
                var download = document.getElementById('st-download')
                var upload = document.getElementById('st-upload')
                var ping = document.getElementById('st-ping')
                var jitter = document.getElementById('st-jitter')
                var ip = document.getElementById('st-ip')

                var data = event.data.split(';')
                var status = Number(data[0])
                if (status >= 4) {
                    clearInterval(interval)
                    document.getElementById('st-start').hidden = false
                    document.getElementById('st-stop').hidden = true
                    w = null
                }
                if (status === 5) {
                    // speedtest cancelled, clear output data
                    data = []
                }
                download.textContent = (status==1&&data[1]==0)?"Starting":data[1]
                upload.textContent = (status==3&&data[2]==0)?"Starting":data[2]
                ping.textContent = data[3]
                ip.textContent = data[4]
                jitter.textContent = data[5]
            }
            worker.postMessage('start')
        }
        function stopTest() {
            if (worker) worker.postMessage('abort')
        }
    </script>
</body>
</html>
