const currencyIcons = {
    USD: "fa-dollar-sign",
    EUR: "fa-euro-sign",
    GBP: "fa-pound-sign",
    JPY: "fa-yen-sign",
    SGD: "fa-dollar-sign",
    HKD: "fa-dollar-sign",
    NOK: "fa-dollar-sign",
    INR: "fa-rupee-sign",
    CNY: "fa-yen-sign"
};

const currencies = ['USD', 'EUR', 'GBP', 'JPY', 'SGD', 'HKD', 'NOK', 'INR', 'CNY'];
let currentPage = 0;
const itemsPerPage = 3;
let previousRates = {};

async function fetchExchangeRates() {
    try {
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await response.json();
        updateCurrencyList(data);
    } catch (error) {
        console.error('Error fetching exchange rates:', error);
    }
}

function updateCurrencyList(data) {
    const currencyList = document.getElementById('currency-list');
    currencyList.innerHTML = '';
    
    const start = currentPage * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedCurrencies = currencies.slice(start, end);

    paginatedCurrencies.forEach(currency => {
        const rate = data.rates['PHP'] / data.rates[currency];
        let change = '';
        let changeClass = '';
        if (previousRates[currency]) {
            const diff = ((rate - previousRates[currency]) / previousRates[currency]) * 100;
            change = diff.toFixed(2) + '%';
            changeClass = diff >= 0 ? 'change' : 'change down';
            change = `<span class="${changeClass}">${diff >= 0 ? '▲' : '▼'} ${change}</span>`;
        }
        previousRates[currency] = rate;

        // Get FontAwesome icon or default
        const currencyIconClass = currencyIcons[currency] || "fa-money-bill-wave";

        const item = document.createElement('div');
        item.className = 'currency-item';
        item.innerHTML = `
            <div class="currency-left">
                <div class="currency-icon">
                    <i class="fa-solid ${currencyIconClass}"></i>
                </div>
                <div class="currency-details">
                    <div>$1 ${currency}</div>
                    <small>${currency}</small>
                </div>
            </div>
            <div class="currency-value">
                <div class="rate">₱${rate.toFixed(2)}</div>
                ${change}
            </div>
        `;
        currencyList.appendChild(item);
    });

    document.getElementById('prev-btn').style.display = currentPage === 0 ? 'none' : 'inline-block';
    document.getElementById('next-btn').disabled = end >= currencies.length;
}

document.getElementById('prev-btn').addEventListener('click', () => {
    if (currentPage > 0) {
        currentPage--;
        fetchExchangeRates();
    }
});

document.getElementById('next-btn').addEventListener('click', () => {
    if ((currentPage + 1) * itemsPerPage < currencies.length) {
        currentPage++;
        fetchExchangeRates();
    }
});

// Calendar Function
function generateCalendar() {
    const now = new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" }));
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const daysOfWeek = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];

    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth();
    const currentDate = now.getDate();

    document.getElementById("month-year").textContent = monthNames[currentMonth];
    document.getElementById("year").textContent = currentYear;

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

    const calendar = document.getElementById("calendar");
    calendar.innerHTML = "";

    daysOfWeek.forEach(day => {
        const dayElement = document.createElement("div");
        dayElement.innerHTML = `<strong>${day}</strong>`;
        calendar.appendChild(dayElement);
    });

    for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
        const emptyDiv = document.createElement("div");
        calendar.appendChild(emptyDiv);
    }

    for (let day = 1; day <= lastDate; day++) {
        const dayElement = document.createElement("div");
        dayElement.classList.add("day");
        dayElement.textContent = day;
        
        if (day === currentDate) {
            dayElement.classList.add("today");
        }

        calendar.appendChild(dayElement);
    }
}

// Run both functions when the page loads
document.addEventListener("DOMContentLoaded", function () {
    generateCalendar();  // Initialize Calendar
    fetchExchangeRates(); // Load Exchange Rates
    setInterval(fetchExchangeRates, 60000); // Auto-refresh every 60 seconds
});
