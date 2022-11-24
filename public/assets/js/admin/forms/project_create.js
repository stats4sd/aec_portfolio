

budgetField = document.getElementById('budget-field');

console.log(budgetField);


budgetField.addEventListener('onfocus', () => {
  const value = node.value;
  node.type = 'number';
  console.log('number');
  node.value = Number(value.replace(/,/g, '')); // or equivalent per locale
});

budgetField.addEventListener('onblur', () => {
  const value = node.value;
  node.type = 'text';
  console.log('test');
  node.value = value.toLocaleString();  // or other formatting
});
