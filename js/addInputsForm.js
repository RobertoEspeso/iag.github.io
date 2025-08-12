let containerRentalCarProgramsInputs = document.getElementById('rental-car-programs-inputs');
let containerHotelProgramsAndCarPreferencesInputs = document.getElementById('container-hotel-programs-and-car-preferences-inputs');
const btnAddInputHotel = document.getElementById('btnAddInputHotel');
const btnAddInputRentalCars = document.getElementById('btnAddInputRentalCars');

let counterRentalCars = 0;
let counterHotel = 0;

function counterHotelID() {
    counterHotel = containerHotelProgramsAndCarPreferencesInputs.querySelectorAll('.inputs-inline').length + 1;
    console.log(counterHotel);
    return counterHotel;
}

function counterRentalCarsID() {
    counterRentalCars = containerRentalCarProgramsInputs.querySelectorAll('.inputs-inline').length + 1;
    console.log(counterRentalCars);
    return counterRentalCars;
}
btnAddInputRentalCars.addEventListener('click', (e) => {
    e.preventDefault();
    addRentalCarsInputs();
})


btnAddInputHotel.addEventListener('click', (e) => {
    e.preventDefault();
    addHotelInputs();
})

function addHotelInputs() {
    counterHotelID()
    containerHotelProgramsAndCarPreferencesInputs.innerHTML += `                            <div class="inputs-inline">
                                <div class="input-left">
                                    <label for="hotel-name-${counterHotel}">Hotel Name</label>
                                    <input type="text" name="hotel-name-${counterHotel}" id="hotel-name-${counterHotel}">
                                </div>
                                <div class="input-right">
                                    <label for="guest-number-${counterHotel}">Guest Number</label>
                                    <input type="text" name="guest-number-${counterHotel}" id="guest-number-${counterHotel}">
                                </div>
                            </div>`
}
function addRentalCarsInputs() {
    counterRentalCarsID()
    containerRentalCarProgramsInputs.innerHTML += `                            <div class="inputs-inline">
                                <div class="input-left">
                                    <label for="car-company-name-${counterRentalCars}">Car Company Name</label>
                                    <input type="text" name="car-company-name-${counterRentalCars}" id="car-company-name-${counterRentalCars}">
                                </div>
                                <div class="input-right">
                                    <label for="account-number-retal-car-${counterRentalCars}">Account Number</label>
                                    <input type="text" name="account-number-retal-car-${counterRentalCars}" id="account-number-retal-car-${counterRentalCars}">
                                </div>
                            </div>`
}