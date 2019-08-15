$(document).ready(function () {
    $("#search").keyup(function () {
        let query = $(this).val();
        console.log(query);
        console.log(query.length)
        if (query.length > 2) {
            $.ajax({
                url: '/ajax/search',
                type: "GET",
                data: {
                    query: query
                }
            }).done(function(data){
                $("#search-results").html(data);
            }).fail(function(xhr, status, error){
                console.log(status);
            })
        }
    });
});
