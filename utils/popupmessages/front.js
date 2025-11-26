function displayPopupMessage(message) {
    // Remove existing popup if present
    const existing = document.getElementById("popupMessage");
    if (existing) existing.remove();

    // Create popup container
    const popup = document.createElement("div");
    popup.id = "popupMessage";

    // Insert popup content
    popup.innerHTML = `
        <button id="closePopup">X</button>
        <span>${message}</span>
    `;

    // Append to page
    document.body.appendChild(popup);

    // Close button functionality
    popup.querySelector("#closePopup").addEventListener("click", () => {
        popup.remove();
    });
}