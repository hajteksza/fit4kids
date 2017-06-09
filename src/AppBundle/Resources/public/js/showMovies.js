$(function(){
$("#mainVideoDiv").css({
                    'height' : '500px',
                    'overflow-y' : 'hidden',
                    });
$("#mainTableDiv").css({
                    'height' : '500px',
                    'overflow-y' : 'scroll',
                    'background-color' : 'azure',
                    });
$(".videoLink").css({
                    'font-size' : '30px',
                    });
$("td").css({
                    'width' : '350px',
                    });
var videoLinks = $(".videoLink")

    videoLinks.click(function(e){
        var link = e.target;
        e.preventDefault();
        var id = link.dataset.id;
        $.ajax({
          method: "GET",
          url: '/movie/getMoviePath',
          data: {id:id}
        })
        .done(function(d){
            $("#video").attr("src",d.path);
         })
        .fail(function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
         })
    });
});