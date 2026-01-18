function toggleSettings() {
    const box = document.getElementById("moreSettings");
    const arrow = document.getElementById("arrow");

    if (box.style.display === "block") {
        box.style.display = "none";
        arrow.style.transform = "rotate(0deg)";
    } else {
        box.style.display = "block";
        arrow.style.transform = "rotate(183deg)";
    }
}
