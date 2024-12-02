window.addEventListener('load', () => {
    const countrybtn = document.getElementById("countrylookup");
    const citybtn = document.querySelector("#citylookup");
    const result = document.querySelector("#result");
    const inputform = document.querySelector("#country");

    
    const fetchData = (lookupType) => {
        const userInput = inputform.value.trim();
        let url;

        if (lookupType === "country") {
            url = `world.php?country=${userInput}&lookup=country`;
        } else if (lookupType === "city") {
            url = `world.php?country=${userInput}&lookup=city`;
        }

        fetch(url)
            .then(response => {
                if (response.ok) {
                    return response.text();
                } else {
                    return Promise.reject('ERROR');
                }
            })
            .then(data => {
                result.innerHTML = data; 
            })
            .catch(error => console.error(`ERROR: ${error}`));
    };

    countrybtn.addEventListener("click", (e) => {
        e.preventDefault();
        fetchData("country");
    });

    citybtn.addEventListener("click", (e) => {
        e.preventDefault();
        fetchData("city");
    });
});




