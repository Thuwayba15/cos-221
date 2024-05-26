function tvShows(){
    for (var i = 1 ; i <= 10 ; i++){
        var id = i;
        var req = new XMLHttpRequest();
        req.open('GET', 'http://api.tvmaze.com/shows/'+id, false);
        req.send(null);
        var data = JSON.parse(req.responseText);
        console.log("tv show " + i + " : ");
        console.log("id : " + data.id);
    }
}