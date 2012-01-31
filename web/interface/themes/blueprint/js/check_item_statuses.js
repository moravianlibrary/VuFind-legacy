$(document).ready(function() {
    checkItemStatuses();
});

function checkItemStatuses() {
    var id = $.map($('.recordId'), function(i) {
        return $(i).attr('id').substr('record'.length);
    });
    if (id.length) {
        $(".ajax_availability").show();
        $.ajax({
            dataType: 'json',
            url: path + '/AJAX/JSON?method=getItemStatuses',
            data: {id:id},
            success: function(response) {
                if(response.status == 'OK') {
                    $.each(response.data, function(i, result) {
                        $('#status' + result.id).empty().append(result.availability_message);
                        if (result.locationList) {
                            $('#callnumber' + result.id).hide();
                            $('.hideIfDetailed' + result.id).hide();
                            $('#location' + result.id).hide();
                            var locationListHTML = "";
                            for (x=0; x<result.locationList.length; x++) {
                                locationListHTML += '<div class="groupLocation">';
                                if (result.locationList[x].availability) {
                                    locationListHTML += '<span class="availableLoc">' 
                                        + result.locationList[x].location + '</span> ';
                                } else {
                                    locationListHTML += '<span class="checkedoutLoc">'  
                                        + result.locationList[x].location + '</span> ';
                                }
                                locationListHTML += '</div>';
                                locationListHTML += '<div class="groupCallnumber">';
                                locationListHTML += (result.locationList[x].callnumbers) 
                                     ?  result.locationList[x].callnumbers : '';
                                locationListHTML += '</div>';
                            }
                            $('#locationDetails' + result.id).show();
                            $('#locationDetails' + result.id).empty().append(locationListHTML);
                        } else {
                            $('#callnumber' + result.id).empty().append(result.callnumber);
                            $('#location' + result.id).empty().append(result.reserve == 'true' ? result.reserve_message : result.location);
                        }
                    });
                } else {
                    // display the error message on each of the ajax status place holder
                    $(".ajax_availability").empty().append(response.data);
                }
                $(".ajax_availability").removeClass('ajax_availability');
            }
        });
    }
}
