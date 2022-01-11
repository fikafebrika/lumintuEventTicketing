const IP = 'lumintu-tiket.tamiaindah.xyz:8055';
const CAROUSEL = $('.owl-carousel');
const TABLE = $('.table');
const BUTTON_PLUS = $('.btn-plus');
let arrayOfSession = [];
let statusOfInput = [{ name: `peserta1`, status: true }];

const ID_EVENT = 2;

$(document).ready(() => {
  // Mengambil Jadwal Hari di sebuah event
  $.ajax({
    url: `http://${IP}/items/day`,
    type: 'GET',
    dataType: 'json',
    success: (data, textStatus, xhr) => {
      const items = data.data;
      items.map((item, index) => {
        let day_name = item.day_name;
        let day_date = item.day_date;
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
          );
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
          );
        }
      });
    },
    complete: () => {
      initializeCarousel();
      sesi();
    },
    error: (xhr, textStatus, errorThrown) => {
      console.log('Error in Database');
    },
  });

  // Mengambil Informasi sebuah event
  $.ajax({
    url: `http://${IP}/items/event/${ID_EVENT}`,
    type: 'GET',
    dataType: 'json',
    beforeSend: () => {
      $('#loader').removeClass('d-none');
    },
    success: (data, textStatus, xhr) => {
      const event = data.data;
      $('.nama-event').text(event.event_name);
      $('.eventClient').text('By ' + event.event_client);
      $('.eventAddress').text(event.event_address);
      $('.eventDesc').text(event.event_desc);
    },
    complete: () => {
      $('#loader').addClass('d-none');
    },
    error: (xhr, textStatus, errorThrown) => {
      console.log('Error in Database');
    },
  });
});

// Mengambil Semua Informasi Session
let sesi = () => {
  $.ajax({
    url: `http://${IP}/items/ticket?fields=ticket_id,ticket_type,ticket_x_session.session_id.*,ticket_x_day.day_id.*`,
    type: 'GET',
    dataType: 'json',
    beforeSend: () => {
      $('.spinner-event').removeClass('d-none');
    },
    success: (data, textStatus, xhr) => {
      const items = data.data;
      items.map((item, index) => {
        let ticket_type = item.ticket_type;
        let ticket_x_session = item.ticket_x_session;
        if (ticket_type.includes('Only')) {
          arrayOfSession.push(new Array());
          ticket_x_session.map((item, keys) => {
            arrayOfSession[index].push(item);
          });
        }
      });
    },
    complete: () => {
      $('.spinner-event').addClass('d-none');
      getSession(0);
    },
    error: (xhr, textStatus, errorThrown) => {
      console.log('Error in Database');
    },
  });
};

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
};

// Mengambil Sesi Sesuai Day
let getSession = (day) => {
  TABLE.html('');
  arrayOfSession[day].map((item) => {
    let session = item.session_id;
    let start_time = session.start_time;
    let finish_time = session.finish_time;
    let session_type = session.session_type;
    let session_desc = session.session_desc;
    let isiTabel = `
      <tr>
          <td class="date-session">${convertTime(start_time)} - ${convertTime(finish_time)}</td>
          <td class="title-session font-weight-bold">${session_type}<br>
              <span class="detail-session font-weight-normal">${session_desc}</span>
          </td>
      </tr>
    `;
    TABLE.append(isiTabel);
  });
};

// Mengubah String menjadi Tipe Data Date
let convertDate = (dateString) => {
  var momentObj = moment(dateString, 'YYYY-DD-MM');
  var momentString = momentObj.format('D MMM yy');
  return momentString;
};

// Mengubah String menjadi Tipe Data Time
let convertTime = (time) => {
  var momentObj = moment(time, 'YYYY-MM-DDTHH:mm:ss');
  var momentString = momentObj.format('hh.mm A');
  return momentString;
};

let toggleButtonTrash = () => {
  let quantity = document.querySelectorAll('.peserta').length;
  if (quantity > 1) {
    $('.btn-delete').prop("disabled", false)
    $('.btn-delete').first().prop("disabled", true)
  } else {
    $('.btn-delete').prop("disabled", true)
  }
}

// Function untuk memperbanyak field untuk mengisi email invitation
let addInputFieldInvitation = () => {
  let quantity = document.querySelectorAll('.peserta').length;
  let elem = document.getElementById('peserta1');
  let cln = elem.cloneNode(true);
  cln.id = 'peserta' + ++quantity;

  $('.body-popup').append(cln);
  toggleButtonTrash()
  statusOfInput.push({ name: `peserta${quantity}`, status: false });
  let jenisPeserta = `peserta${quantity}`
  $(`#${jenisPeserta} .special`).remove();
  $(`#${jenisPeserta} p`).text(`Peserta ${quantity}`);
  $(`#${jenisPeserta} input`).attr({
    name: `peserta${quantity}`,
    id: `peserta${quantity}`,
  });
  $(`#${jenisPeserta} input`).val('');
  $(`#${jenisPeserta} input`).prop('readonly', false)
  validate(`peserta${quantity}`);
  $(`#${jenisPeserta} #emailHelpBlock`).removeClass('d-none');
  $(`#${jenisPeserta} .row .col-2`).html(
    `<button class="btn btn-danger btn-delete btn-sm rounded-0" type="button"
    data-toggle="tooltip" data-placement="top" title="Delete" onclick="deleteField('${jenisPeserta}')"><i class="fa fa-trash"></i></button>`
  );

  // $('.btn-delete').click(function () {
  //   $(this).parent().parent().parent().remove();
  //   statusOfInput.pop()
  //   toggleButtonTrash()
  //   checkStatus();
  //   return false;
  // });
};

const checkStatus = () => {
  if (statusOfInput.find((element) => element.status === false) === undefined) {
    $('.btn-invite').prop('disabled', false);
  } else {
    $('.btn-invite').prop('disabled', true);
  }
  return false;
}

const deleteField = (fieldNameClass) => {
  $(`#${fieldNameClass}`).remove()
  console.log(fieldNameClass)
  statusOfInput.splice(statusOfInput.findIndex(element => element.name === fieldNameClass), 1)
  checkStatus()

  statusOfInput.map((item, index) => {
    $(`#${item.name} .row .urutan`).text(`Peserta ${index + 1}`)
    $(`#${item.name} input`).attr({
      'name': `peserta${index + 1}`,
      'id': `peserta${index + 1}`
    })
    item.name = `peserta${index + 1}`
  })
}



// Function Validate Email dengan REGEX
let validateEmail = (email) => {
  const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
};

// Function untuk melakukan validasi dari sebuah input
let validate = (email) => {
  // console.log(validateEmail(email))
  // if (validateEmail(email)){

  // }
  const RESULT = $(`#${email} #emailHelpBlock`);
  if (validateEmail($(`[name=${email}]`).val())) {
    RESULT.text('Your email is valid');
    RESULT.addClass('d-none');
    statusOfInput.find((item, index) => {
      if (item.name === `${email}`) {
        let oldData = statusOfInput[index];
        statusOfInput[index] = { ...oldData, status: true };
        return true;
      }
    });
  } else {
    RESULT.text('Your email is not valid');
    statusOfInput.find((item, index) => {
      if (item.name === `${email}`) {
        let oldData = statusOfInput[index];
        statusOfInput[index] = { ...oldData, status: false };
        return true;
      }
    });
  }

  checkStatus()
};

// Show Voucher Code Field
$(document).on('change', '.switchMe', function () {
  let oldData = statusOfInput[0];
  if (this.checked) {
    $('#peserta1 #emailHelpBlock').addClass('d-none');
    BUTTON_PLUS.addClass('d-none')
    $(".input-voucher").removeClass("d-none")
    statusOfInput[0] = { ...oldData, status: true };
    validate('peserta1');
    $(".peserta").not(':first').remove();
  } else {
    $('#peserta1 #emailHelpBlock').removeClass('d-none');
    BUTTON_PLUS.removeClass('d-none')
    $(".input-voucher").addClass("d-none")
    statusOfInput[0] = { ...oldData, status: false };
    validate('peserta1');
  }
});
$('input#voucher').on('input', () => {
  if ($(this).val() != '') {
    BUTTON_PLUS.addClass('d-none');
  } else {
    BUTTON_PLUS.removeClass('d-none');
  }
});

document.querySelector('.body-form').addEventListener('submit', function (e) {
  var form = this;

  e.preventDefault(); // <--- prevent form from submitting

  swal
    .fire({
      title: 'Are you sure?',
      text: 'You will not be able to change your invitation!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, I am Sure',
      cancelButtonText: 'No, cancel it!',
      dangerMode: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        swal
          .fire({
            title: 'Success',
            text: `Your Invitation is Completed! Please check the invitee's email`,
            icon: 'success',
          })
          .then(function () {
            form.submit();
          });
      } else if (result.isDenied) {
        swal.fire('Cancelled', 'Make sure your invitation email!', 'error');
      }
    });
});
