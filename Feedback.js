document.addEventListener('DOMContentLoaded', () => {
  const apiWrapper = document.getElementById('wrap_Inputfield_api');

  if (apiWrapper) {
    const api = apiWrapper.querySelector('.InputfieldContent');
    const data = JSON.parse(api.textContent, true);
    let dataCount = Object.keys(data).length;
    const table = document.createElement('table');
    const tableheader = '<thead><tr><th>Name</th><th>Key</th><th>Secret</th><th>Email</th><th></th></tr></thead>';
    const plus = document.createElement('div');
    const plusContent = '<a href="#" class="js-feedback-plus"><i class="fa fa-plus-square"></i> Add Row</a>';
    const trash = document.createElement('td');
    const trashContent = '<a href="#" class="js-feedback-trash"><i class="fa fa-trash"></i></a>';
    let initial = true;

    // add input Element
    const addInput = (key, prop) => {
      const input = document.createElement('input');

      input.type = 'text';
      input.name = `apidata[${key}][${prop}]`;

      if (data[key]) input.value = data[key][prop];

      return input;
    };

    // delete row
    const addTrashFunc = (i) => {
      i.addEventListener('click', e => {
        e.stopPropagation();
        e.preventDefault();
        e.target.parentNode.parentNode.parentNode.outerHTML = '';
      });
    };

    // create Table
    const createTable = () => {
      // add table content
      for (const key in data) {
        const wrapper = document.createElement('tr');

        [ 'name', 'key', 'secret', 'email' ].forEach(prop => {
          const td = document.createElement('td');
          const input = addInput(key, prop);

          td.appendChild(input);
          wrapper.appendChild(td);
        });

        // add trash icon
        if (initial) {
          initial = false;
        } else {
          wrapper.appendChild(trash.cloneNode(true));
        }

        table.appendChild(wrapper);
      }

      api.appendChild(document.createElement('br'));
      api.appendChild(table);
      api.appendChild(plus);
    };

    // init trash functionality
    const initTrash = () => {
      const trashIcons = api.querySelectorAll('.js-feedback-trash');

      [ ...trashIcons ].forEach(i => {
        addTrashFunc(i);
      });
    };

    // clone last element
    const initClone = () => {
      api.querySelector('.js-feedback-plus').addEventListener('click', e => {
        e.preventDefault();

        const wrapper = document.createElement('tr');
        const i = trash.cloneNode(true);

        [ 'name', 'key', 'secret', 'email' ].forEach(prop => {
          const td = document.createElement('td');
          const input = addInput(dataCount, prop);

          td.appendChild(input);
          wrapper.appendChild(td);
        });

        dataCount += 1;
        wrapper.appendChild(i);
        table.appendChild(wrapper);
        addTrashFunc(i);
      });
    };

    const init = () => {
      table.innerHTML = tableheader;
      plus.innerHTML = plusContent;
      trash.innerHTML = trashContent;
      createTable();
      initClone();
      initTrash();
    };

    // INIT
    init();
  }
}, false);
