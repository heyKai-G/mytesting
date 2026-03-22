document.getElementById('form').addEventListener('submit', function (e) {
    e.preventDefault();
 
    const errors = [];
 
    // --- Name: letters and spaces only ---
    const fullName = document.querySelector('[name="fullName"]').value.trim();
    if (!fullName) {
        errors.push("Name is required.");
    } else if (!/^[A-Za-z\s]+$/.test(fullName)) {
        errors.push("Name must contain letters only.");
    }
 
    // --- Age: must be between 18 and 100 ---
    const age = parseInt(document.querySelector('[name="age"]').value.trim());
    if (!age && age !== 0) {
        errors.push("Age is required.");
    } else if (isNaN(age) || age < 18 || age > 100) {
        errors.push("Age must be a realistic value between 18 and 100.");
    }
 
    // --- Address: required ---
    const address = document.querySelector('[name="address"]').value.trim();
    if (!address) {
        errors.push("Address is required.");
    }
 
    // --- Date: required and must be today ---
    const dateVal = document.querySelector('[name="date"]').value;
    if (!dateVal) {
        errors.push("Date is required.");
    } else {
        const [year, month, day] = dateVal.split('-').map(Number);
        const inputDate = new Date(year, month - 1, day); // local time, no timezone shift
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (inputDate.getTime() !== today.getTime()) {
            errors.push("Date must be today's date.");
        }
    }
 
    // --- Service Rows: all 3 required ---
    const serviceTypes = document.querySelectorAll('[name="serviceType[]"]');
    const copies = document.querySelectorAll('[name="copies[]"]');
    const urgencies = document.querySelectorAll('[name="urgency[]"]');
 
    serviceTypes.forEach((select, i) => {
        const rowNum = i + 1;
 
        if (!select.value) {
            errors.push(`Row ${rowNum}: Service Type is required.`);
        }
 
        const copyVal = parseInt(copies[i].value);
        if (!copies[i].value.trim()) {
            errors.push(`Row ${rowNum}: Copies is required.`);
        } else if (isNaN(copyVal) || copyVal < 1) {
            errors.push(`Row ${rowNum}: Copies must be at least 1.`);
        }
 
        if (!urgencies[i].value) {
            errors.push(`Row ${rowNum}: Urgency is required.`);
        }
    });
 
    // --- Show or clear toast ---
    Toas(errors);
 
    if (errors.length === 0) {
        this.submit();
    }
});
 
function Toas(errors) {
    // Remove existing toast if any
    const existing = document.getElementById('validation-toast');
    if (existing) existing.remove();
 
    if (errors.length === 0) return;
 
    const toast = document.createElement('div');
    toast.id = 'validation-toast';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
        border-radius: 8px;
        padding: 16px 24px;
        max-width: 480px;
        width: 90%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-size: 14px;
    `;
 
    const title = document.createElement('strong');
    title.textContent = 'Please fix the following errors:';
    toast.appendChild(title);
 
    const list = document.createElement('ul');
    list.style.marginTop = '8px';
    list.style.marginBottom = '0';
    list.style.paddingLeft = '20px';
    errors.forEach(err => {
        const li = document.createElement('li');
        li.textContent = err;
        list.appendChild(li);
    });
    toast.appendChild(list);
 
    // Close button
    const close = document.createElement('span');
    close.textContent = 'close';
    close.style.cssText = `
        position: absolute;
        top: 8px;
        right: 12px;
        cursor: pointer;
        font-weight: bold;
    `;
    close.onclick = () => toast.remove();
    toast.appendChild(close);
 
    document.body.appendChild(toast);
 
    // Auto-dismiss after 6 seconds
    setTimeout(() => { if (toast) toast.remove(); }, 6000);
}