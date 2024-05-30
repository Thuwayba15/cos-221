//const { get } = require("express/lib/response");

function removeHtmlTags(str) {
    return str.replace(/<[^>]*>/g, '');
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function addEscapeCharacter(str) {
    return str.replace(/['"]/g, '\\$&');
}

function convertCurrencyToNumber(currency) {
    currency = currency.replace(/[$,]/g, '');
    return parseFloat(currency);
}

function convertDate(date) {
    const dateObj = new Date(date);
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getFirstLanguage(languages) {
    return languages.split(',')[0].trim();
}

function getNumberFromMinutes(minutes) {
    return parseInt(minutes.split(' ')[0], 10);
}

function tvShows(){
    for (var i = 1 ; i <= 103; i++){
        if (i == 17 || i == 36 || i == 85){
            continue;
        }
        var id = i;
        var req = new XMLHttpRequest();
        req.open('GET', 'http://api.tvmaze.com/shows/'+id+"?embed=cast", false);
        req.send(null);
        var data = JSON.parse(req.responseText);
        var title = data.name;
        title = addEscapeCharacter(title);
        var language = data.language;
        var genre = data.genres[0];
        var releaseDate = data.premiered;
        var rating = Number(data.rating.average);
        var image = data.image.original;
        var summary = data.summary;
        var runtime = Number(data.averageRuntime);
        var status = data.status;
        var seasons = getRandomInt(1, 10);
        summary = summary.replace(/\u003C/g, '<').replace(/\u003E/g, '>');
        summary = removeHtmlTags(summary);
        summary = addEscapeCharacter(summary);
        var productionStudio = data.network && data.network.name ? data.network.name : "CBS";
        productionStudio = addEscapeCharacter(productionStudio);

        var actorData = data["_embedded"]["cast"];
        document.getElementById("tvResults").innerHTML += "<br>" + data["_embedded"]["cast"].length + "<br>";
        var crew = [];
        var actor;
        for (var j = 0 ; j < actorData.length ; j++){
            actor = {
                "name": actorData[j]["person"] && actorData[j]["person"]["name"] ? actorData[j]["person"]["name"] : null,
                "birthday": actorData[j]["person"] && actorData[j]["person"]["birthday"] ? actorData[j]["person"]["birthday"] : null,
                "deathday": actorData[j]["person"] && actorData[j]["person"]["deathday"] ? actorData[j]["person"]["deathday"] : null,
                "country": actorData[j]["person"] && actorData[j]["person"]["country"] && actorData[j]["person"]["country"]["name"] ? actorData[j]["person"]["country"]["name"] : null,
                "image": actorData[j]["person"] && actorData[j]["person"]["image"] && actorData[j]["person"]["image"]["original"] ? actorData[j]["person"]["image"]["original"] : null,
                "role": "Actor"
            };
            crew.push(actor);
        }

        var req3 = new XMLHttpRequest();
        req3.open('GET', 'http://api.tvmaze.com/shows/'+id+"/crew", false);
        req3.send(null);
        var crewData = JSON.parse(req3.responseText);
        var crewMember;
        for (var j = 0 ; j < crewData.length ; j++){
            crewMember = {
                "name": crewData[j]["person"] && crewData[j]["person"]["name"] ? crewData[j]["person"]["name"] : null,
                "birthday": crewData[j]["person"] && crewData[j]["person"]["birthday"] ? crewData[j]["person"]["birthday"] : null,
                "deathday": crewData[j]["person"] && crewData[j]["person"]["deathday"] ? crewData[j]["person"]["deathday"] : null,
                "country": crewData[j]["person"] && crewData[j]["person"]["country"] && crewData[j]["person"]["country"]["name"] ? crewData[j]["person"]["country"]["name"] : null,
                "image": crewData[j]["person"] && crewData[j]["person"]["image"] && crewData[j]["person"]["image"]["original"] ? crewData[j]["person"]["image"]["original"] : null,
                "role": crewData[j]["type"] ? crewData[j]["type"] : null
            };
            crew.push(crewMember);
        }

        var details = {
            "type": "addTV",
            "title": title,
            "language": language,
            "genre": genre,
            "releaseDate": releaseDate,
            "rating": rating,
            "productionStudio": productionStudio,
            "image": image,
            "summary": summary,
            "status": status,
            "runtime": runtime,
            "seasons": seasons,
            "crew": crew
        };
        var paramStr = JSON.stringify(details);
        var req2 = new XMLHttpRequest();
        req2.open('POST', 'https://wheatley.cs.up.ac.za/u21554995/database.php', false);
        var username = 'u21554995';
        var password = 'Milochetty2010';
        var credentials = btoa(username + ':' + password);
        req2.setRequestHeader('Authorization', 'Basic ' + credentials);
        req2.setRequestHeader('Content-type', 'application/json');
        req2.send(paramStr); 
        document.getElementById("tvResults").innerHTML += "<br>" + crew[0]["name"] + "<br>";
        document.getElementById("tvResults").innerHTML += "<br>" + crew.length + "<br>";
    }
}

function movies(){
    var imdbID = ["tt10804786","tt10888456","tt10888708","tt11430264","tt11697484","tt11769162","tt1179781","tt11819890","tt11875456","tt1206326","tt1252596","tt12663250","tt12730310","tt1285241","tt13412252","tt13669344","tt13853720","tt1401152","tt1481363","tt15250050","tt15327306","tt1542768","tt1703048","tt17491040","tt1836912","tt19783642","tt2287851","tt2367996","tt2375379","tt2499472","tt2724532","tt2763724","tt2870834","tt3949626","tt4139588","tt4479380","tt4642044","tt4649162","tt4652532","tt4769836","tt4779326","tt4906960","tt4944352","tt5074352","tt5242900","tt5248968","tt5249376","tt5351044","tt5659164","tt5659172","tt5741304","tt5923040","tt5969696","tt5998744","tt6167894","tt6527456","tt6547170","tt6733874","tt6843446","tt7099076","tt7254796","tt7268738","tt7448180","tt7511008","tt7550000","tt7587604","tt7594584","tt7601480","tt7737528","tt7983712","tt7983744","tt8055888","tt8106534","tt8368408","tt8531618","tt8615822","tt8688634","tt8836988","tt8851668","tt8870574","tt8972556","tt9066070","tt9228950","tt9426186","tt9531772","tt9815714","tt9845564","tt9898858"];
    var psArr = ["Warner Bros.", "Universal Pictures", "Paramount Pictures", "20th Century Fox", "Columbia Pictures", "Metro-Goldwyn-Mayer (MGM)", "Marvel Studios", "Pixar Animation Studios", "DreamWorks Animation", "Illumination Entertainment", "Lionsgate Films", "Studio Ghibli", "Sony Pictures Animation", "Blue Sky Studios", "A24", "Focus Features", "New Line Cinema", "Legendary Entertainment", "Touchstone Pictures", "Miramax Films"];
    for (var i = 0 ; i < imdbID.length ; i++){
        var id = imdbID[i];
        var psID = getRandomInt(0, 19);
        var req = new XMLHttpRequest();
        req.open('GET', 'http://www.omdbapi.com/?i='+id+'&plot=full&apikey=e25adcac', false);
        req.send(null);
        var data = JSON.parse(req.responseText);
        
        var title = data.Title;
        title = addEscapeCharacter(title);
        var language = getFirstLanguage(data.Language);
        var genre = data.Genre.split(',')[0].trim();
        var releaseDate = convertDate(data.Released);
        var rating = Number(data.imdbRating);
        var productionStudio = psArr[psID];
        productionStudio = addEscapeCharacter(productionStudio);
        var image = data.Poster;
        var summary = data.Plot;
        summary = addEscapeCharacter(summary);
        var runtime = getNumberFromMinutes(data.Runtime);
        var awards = data.Awards;
        var age_rating = data.Rated;
        var box_office = convertCurrencyToNumber(data.BoxOffice);

        var crew = [];
        var allActors = data.Actors.split(',');
        for (var j = 0 ; j < allActors.length ; j++){
            var actor = {
                "name": allActors[j].trim(),
                "birthday": null,
                "deathday": null,
                "country": null,
                "image": null,
                "role": "Actor"
            };
            crew.push(actor);
        }
        var allWriters = data.Writer.split(',');
        for (var j = 0 ; j < allWriters.length ; j++){
            var writer = {
                "name": allWriters[j].trim(),
                "birthday": null,
                "deathday": null,
                "country": null,
                "image": null,
                "role": "Writer"
            };
            crew.push(writer);
        }
        var allDirectors = data.Director.split(',');
        for (var j = 0 ; j < allDirectors.length ; j++){
            var director = {
                "name": allDirectors[j].trim(),
                "birthday": null,
                "deathday": null,
                "country": null,
                "image": null,
                "role": "Director"
            };
            crew.push(director);
        }

        var details = {
            "type": "addMovie",
            "title": title,
            "language": language,
            "genre": genre,
            "releaseDate": releaseDate,
            "rating": rating,
            "productionStudio": productionStudio,
            "image": image,
            "summary": summary,
            "runtime": runtime,
            "awards": awards,
            "age_rating": age_rating,
            "box_office": box_office,
            "crew": crew
        };
        
        var paramStr = JSON.stringify(details);
        var req2 = new XMLHttpRequest();
        req2.open('POST', 'https://wheatley.cs.up.ac.za/u21554995/database.php', false);
        var username = 'u21554995';
        var password = 'Milochetty2010';
        var credentials = btoa(username + ':' + password);
        req2.setRequestHeader('Authorization', 'Basic ' + credentials);
        req2.setRequestHeader('Content-type', 'application/json');
        req2.send(paramStr);

        document.getElementById("movieResults").innerHTML += "<br>" + data.Title + "<br>";
    }
}

function getContent(){
    var param = {
        "type": "getContent"
    };
    var paramStr = JSON.stringify(param);
    var req = new XMLHttpRequest();
    req.open('POST', 'https://wheatley.cs.up.ac.za/u21554995/COS221/221api.php', false);
    var username = "u21554995";
    var password = "Milochetty2010";
    var credentials = btoa(username + ':' + password);
    req.setRequestHeader('Authorization', 'Basic ' + credentials);
    req.setRequestHeader('Content-type', 'application/json');
    req.send(paramStr);
    var data = JSON.parse(req.responseText);
    document.getElementById("contentResults").innerHTML = JSON.stringify(data);
}

//awards age_rating box_office