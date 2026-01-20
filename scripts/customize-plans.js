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

// Edit day count
const daysBox = document.getElementById("daysBox");
const editDays = document.getElementById("editDays");
const daysInput = document.getElementById("daysInput");

editDays.addEventListener("click", function () {
    let input = document.createElement("input");
    input.type = "number";
    input.min = 1;
    input.value = daysBox.textContent;
    input.style.width = "50px";

    daysBox.replaceWith(input);
    input.focus();

    const saveValue = () => {
        let newValue = input.value.trim() || "30";
        daysBox.textContent = newValue;
        daysInput.value = newValue;
        input.replaceWith(daysBox);
    };

    input.addEventListener("blur", saveValue);
    input.addEventListener("keydown", function (e) {
        if (e.key === "Enter"){
            e.preventDefault();
            saveValue();
        } 
    });
});
