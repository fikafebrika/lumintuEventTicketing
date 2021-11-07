const IP = "192.168.0.125:8001"
const CAROUSEL = $('.owl-carousel')
const TABLE = $('.table')
const BUTTON_PLUS = $(".btn-plus")
let arrayOfSession = []


const ID_EVENT = 2;

$(document).ready(() => {
    // Mengambil Jadwal Hari di sebuah event
    $.ajax({
        url: `http://${IP}/items/day`,
        type: "GET",
        dataType: "json",
        success: (data, textStatus, xhr) => {
            const items = data.data
            items.map((item, index) => {
                let day_name = item.day_name
                let day_date = item.day_date
                if (index == 0) {
                    CAROUSEL.append(
                        `
                        <div class="item border-carousel-item active">
                            <a href="#" onclick="getSession(${index})">
                                <p class="h5">${day_name}</p>
                                <p class="tanggal-event">${convertDate(day_date)}</p>
                            </a>
                        </div>
                    `
                    )
                } else {
                    CAROUSEL.append(
                        `
                        <div class="item border-carousel-item">
                            <a href="#" onclick="getSession(${index})">
                                <p class="h5">${day_name}</p>
                                <p class="tanggal-event">${convertDate(day_date)}</p>
                            </a>
                        </div>
                    `
                    )
                }
            })
        },
        complete: () => {
            initializeCarousel()
            sesi()
        },
        error: (xhr, textStatus, errorThrown) => {
            console.log("Error in Database");
        },
    });

    // Mengambil Informasi sebuah event
    $.ajax({
        url: `http://${IP}/items/event/${ID_EVENT}`,
        type: "GET",
        dataType: "json",
        beforeSend: () => {
            $("#loader").removeClass('d-none');
        },
        success: (data, textStatus, xhr) => {
            const event = data.data;
            $(".nama-event").text(event.event_name);
            $(".eventClient").text("By " + event.event_client);
            $(".eventAddress").text(event.event_address);
            $(".eventDesc").text(event.event_desc);
        },
        complete: () => {
            $("#loader").addClass('d-none');
        },
        error: (xhr, textStatus, errorThrown) => {
            console.log("Error in Database");
        },
    });

})

// Mengambil Semua Informasi Session 
let sesi = () => {
    $.ajax({
        url: `http://${IP}/items/ticket?fields=ticket_id,ticket_type,ticket_x_session.session_id.*,ticket_x_day.day_id.*`,
        type: "GET",
        dataType: "json",
        beforeSend: () => {
            $(".spinner-event").removeClass('d-none');
        },
        success: (data, textStatus, xhr) => {
            const items = data.data
            items.map((item, index) => {
                let ticket_type = item.ticket_type
                let ticket_x_session = item.ticket_x_session
                if (ticket_type.includes("Only")) {
                    arrayOfSession.push(new Array())
                    ticket_x_session.map((item, keys) => {
                        arrayOfSession[index].push(item)
                    })
                }
            })
        },
        complete: () => {
            $(".spinner-event").addClass('d-none');
            getSession(0)
        },
        error: (xhr, textStatus, errorThrown) => {
            console.log("Error in Database");
        },
    });
}

// Function untuk Inisialisasi Ulang Carousel
let initializeCarousel = () => {
    CAROUSEL.owlCarousel({
        margin: 5,
        nav: true,
        responsive: {
            0: {
                items: 3,
            },
            600: {
                items: 3,
            },
            1000: {
                items: 4,
            },
        },
    });
}

// Mengambil Sesi Sesuai Day
let getSession = (day) => {
    TABLE.html("")
    arrayOfSession[day].map(item => {
        let session = item.session_id
        let start_time = session.start_time
        let finish_time = session.finish_time
        let session_type = session.session_type
        let session_desc = session.session_desc
        let isiTabel =
            `
            <tr>
                <td class="date-session">${convertTime(start_time)} - ${convertTime(finish_time)}</td>
                <td class="title-session font-weight-bold">${session_type}<br>
                    <span class="detail-session font-weight-normal">${session_desc}</span>
                </td>
            </tr>
        `
        TABLE.append(isiTabel)
    })
}

// Mengubah String menjadi Tipe Data Date
let convertDate = (dateString) => {
    var momentObj = moment(dateString, "YYYY-DD-MM");
    var momentString = momentObj.format("D MMM yy");
    return momentString;
}

// Mengubah String menjadi Tipe Data Time
let convertTime = (time) => {
    var momentObj = moment(time, "YYYY-MM-DDTHH:mm:ss");
    var momentString = momentObj.format("hh.mm A");
    return momentString;
}

// Function untuk memperbanyak field untuk mengisi email invitation
let addInputFieldInvitation = () => {
    let quantity = document.querySelectorAll(".peserta").length;
    let elem = document.getElementById("peserta1");
    let cln = elem.cloneNode(true);
    cln.id = "peserta" + ++quantity;

    $(".body-popup").append(cln);
    document.querySelector;
    $(`#peserta${quantity} .special`).remove();
    $(`#peserta${quantity} p`).text(`Peserta ${quantity}`);
    $(`#peserta${quantity} .form-group input`).attr({
        name: `peserta${quantity}`,
        id: `peserta${quantity}`,
        value: ""
    })
    $(`#peserta${quantity} #emailHelpBlock`).removeClass('d-none')
}

// Function Validate Email dengan REGEX
let validateEmail = (email) => {
    const re =
        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

// Function untuk melakukan validasi dari sebuah input
let validate = (email) => {
    const RESULT = $(`#${email} #emailHelpBlock`);
    if (validateEmail($(`[name=${email}]`).val())) {
        RESULT.text("Your email is valid");
        RESULT.addClass('d-none')
    } else {
        RESULT.text("Your email is not valid");
    }
    return false;
}

// Show Voucher Code Field
$(document).on("change", ".switchMe", () => {
    if (this.checked) {
        $("input#voucher").css("visibility", "visible");
    } else {
        $("input#voucher").css("visibility", "hidden");
    }
});

$("input#voucher").on("input", () => {
    if ($(this).val() != "") {
        BUTTON_PLUS.addClass("d-none");
    } else {
        BUTTON_PLUS.removeClass("d-none");
    }
});


