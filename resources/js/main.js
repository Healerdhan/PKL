// document.addEventListener('DOMContentLoaded', function () {
//   fetch('/api/category')
//       .then(response => response.json())
//       .then(data => {
//           const categoryTableBody = document.querySelector('#category-table tbody');
//           data.data.forEach(category => {
//               const row = document.createElement('tr');
//               row.innerHTML = `
//                   <td>${category.id}</td>
//                   <td>${category.jurusan}</td>
//                   <td>${category.created_at}</td>
//                   <td>${category.updated_at}</td>
//               `;
//               categoryTableBody.appendChild(row);
//           });
//       })
//       .catch(error => console.error('Error fetching data:', error));
// });
