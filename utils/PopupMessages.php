<?php
function setNewPopupMessage($message) {
    $_SESSION['popupMessage'] = $message;
}

function displayPopupMessage() {
    if (isset($_SESSION['popupMessage'])) {
        echo '
        <div id="popupMessage"> 
            <button id="closePopup">X</button> 
            <span>' . $_SESSION['popupMessage'] . '</span> 
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var closeBtn = document.getElementById("closePopup");
            if (closeBtn) {
                closeBtn.addEventListener("click", function() {
                    var popup = document.getElementById("popupMessage");
                    if (popup) {
                        popup.style.display = "none";
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
