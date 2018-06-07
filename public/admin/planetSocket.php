<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}

include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div class="container-fluid main_center">
    <ol class="breadcrumb" style="margin-top: 20px;">
      <li class="breadcrumb-item active">Socket</li>
    </ol>
    <div class="col-md-12 content">
        <h2 style="padding-top: 10px;">Display Results <i class="fa fa-qrcode"></i></h2>
        <?php if (!empty($message)) : ?>
            <div class="container alert alert-success message">
            <span class="glyphicon glyphicon-ok"></span> 
            <?php echo output_message($message); ?>
        <?php else : ?>
            <?php
            $msg  = $session->real_name;
            $msg .= ", you are logged in to gym: ";
            $msg .= $session->gym_name;
            $msg .= " as ";
            $msg .= $session->role_name;
            $msg .= ".";
            ?>
            <div class="container alert alert-info message">
            <span class="glyphicon glyphicon-info-sign"></span> 
            <?php echo output_message($msg); ?>
        <?php endif; ?>
            </div><!-- /.alert alert-info message -->

        <div class="container">
            <h1>WORK IN PROGRESS</h1>
            <form data-toggle="validator" id="socketForm" class="form-horizontal" action="planetSocket.php"
                method="post">
                <div class="form-group row form_space has-feedback">
                    <label for="barcodeInput" class="col-xs-2 col-form-label">BARCODE:
                    </label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-barcode"></i>
                            </span>
                            <input class="form-control" type="text" name="barcodeInput"
                            id="barcodeInput" pattern="[0-9]{6}$" maxlength=6 
                            data-error="Barcode number cannot be blank and must consists 
                            of exactly 6 digits." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group --> 
                        <div class="help-block with-errors">Enter barcode number.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space has-feedback -->
                <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
                  <button type="submit" name="send_barcode" class="btn btn-success pull-right">Send Barcode to Server
                    <i class="fa fa-play"></i></button>
                </div>
            </form>
            <div class="jumbotron" style="margin-top: 40px;">
                <div class="page-header">
                    <h1>Display data!</h1>
                </div>
                <div class="media">
                    <div class="media-left">
                        <a id="imageAnchor" href="">
                            <img class="media-object" id="upoaded_image" src="" alt="image of customer">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Media heading</h4>
                        <div id="json_data"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
    </div><!-- /.jumbotron page-header -->
</div><!-- /.container-fluid .main_center-->
<script type="text/javascript">
    var conn = new WebSocket('wss://planet-portal.info/wss');

    $("#socketForm").submit(function(event){
        // cancels the form submission
        event.preventDefault();
        console.log("Form submission canceled.");
        var name = $("#barcodeInput").val();
        console.log(name);
        conn.send(name);
    });
    
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log("Attributes of object received");
        try {
            console.log(e.data);
            var obj = JSON.parse(e.data);
        } catch(e) {
            console.log(e.data);
        }
        // var obj = JSON.parse(e.data);
        if (obj) {
            n = obj.image_path.indexOf("uploads");
            res = obj.image_path.substr(n);
            console.log(res);
            var image = document.getElementById("upoaded_image");
            image.src = "../../" + res;
            document.getElementById("imageAnchor").href = "../../" + res;
            document.getElementById("json_data").innerHTML =  "Name: " + obj.name + ", Gym_name:  " + obj.gym_name + ", Id: " + obj.id + ", Barcode: " + obj.barcode;
            console.log(obj.name);
            console.log(obj.gym_name);
            console.log(obj.image_path);
            console.log(obj.id);
            console.log(obj.barcode);
        }
    };
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>
