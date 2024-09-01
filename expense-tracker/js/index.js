const table = document.querySelector("#items-table");
const pagination = document.querySelector("#pagination");
const itemName = document.querySelector("#item-name");
const itemAmount = document.querySelector("#item-amount");
let month = 1;

async function initData() {
  const result = await fetch("items.php");
  const json = await result.json();
  const months = json.months;
  month = months[0];
  for (const m of months) {
    const mItem = document.createElement("li");
    mItem.classList.add("page-item");
    mItem.innerHTML = <a class="page-link" href="#">${m}</a>;
    pagination.appendChild(mItem);
  }
  const addItem = document.createElement("li");
  addItem.classList.add("page-item");
  addItem.innerHTML = <a class="page-link" href="#">+</a>;
  pagination.appendChild(addItem);
}

document.querySelector("#add-item").addEventListener("click", () => {
  const name = itemName.value;
  const amount = itemAmount.value;
  console.log(name, amount);
});

initData();