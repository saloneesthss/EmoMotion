function handleFavoriteClick(videoId, isPlan, iconElement) {
    let loggedIn = iconElement.getAttribute("data-loggedin");

    if (loggedIn === "0") {
        window.location.href = "../login.php";
        return;
    }

    toggleFavorite(videoId, isPlan, iconElement);
}

function toggleFavorite(id, isPlan, iconElement) {
    let formData = new FormData();
    formData.append("id", id);
    formData.append("isPlan", isPlan);

    fetch("../pages/toggle-favorite.php", {
        method: "POST",
        body: formData
    })
    .then(async res => {
        let raw = await res.text(); 
        console.log("RAW RESPONSE:", raw);
        return JSON.parse(raw); 
    })
    .then(data => {
        if (data.status === "added") {
            iconElement.classList.add("favorited");
            console.log("added");
        } 
        else if (data.status === "removed") {
            iconElement.classList.remove("favorited");
        }

    })
    .catch(err => console.log("ERROR:", err));
}
