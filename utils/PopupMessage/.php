<?php
function setPopupMessage($message) {
    $_SESSION['popupMessage'] = $message;
}

function displayPopupMessage() {
    if (isset($_SESSION['popupMessage'])) {
        echo '
        <div id="popupMessage"> 
            <span>' . $_SESSION['popupMessage'] . '</span>
            <button id="closePopup">OK</button> 
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var closeBtn = document.getElementById("closePopup");
            if (closeBtn) {
                closeBtn.addEventListener("click", function() {
                    var popup = document.getElementById("popupMessage");
                    if (popup) {
                        popup.remove();
                    }
                });
            }
        });
        </script>
        ';
        unset($_SESSION['popupMessage']);
    }
}
?>
