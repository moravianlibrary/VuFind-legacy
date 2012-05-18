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
                        var safeId = jqEscape(result.id);
                        $('#status' + safeId).empty().append(result.availability_message);
                        if (typeof(result.full_status) != 'undefined'
                            && result.full_status.length > 0
                            && $('#callnumAndLocation' + safeId).length > 0
                        ) {
                            $('#callnumAndLocation' + safeId).empty().append(result.full_status);
                            $('#callnumber' + safeId).hide();
                            $('#location' + safeId).hide();
                            $('.hideIfDetailed' + safeId).hide();
                            $('#status' + safeId).hide();
                        } else if (result.locationList) {
                            $('#callnumber' + safeId).hide();
                            $('.hideIfDetailed' + safeId).hide();
                            $('#location' + safeId).hide();
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
                            $('#locationDetails' + safeId).show();
                            $('#locationDetails' + safeId).empty().append(locationListHTML);
                        } else {
                            $('#callnumber' + safeId).empty().append(result.callnumber);
                            $('#location' + safeId).empty().append(
                                result.reserve == 'true' 
                                ? result.reserve_message 
                                : result.location
                            );
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
