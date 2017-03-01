document.addEventListener('DOMContentLoaded', () => {
  const elems = [ 'name', 'key', 'secret', 'email' ];
  const classes = {
    wrapper: 'Inputfield_api',
    trash: 'js-feedback-trash',
    plus: 'js-feedback-plus'
  };

  const apiWrapper = document.querySelector(`.${classes.wrapper}`);

  if (apiWrapper) {
    const api = apiWrapper.querySelector('.InputfieldContent');
    const data = JSON.parse(api.textContent, true);
    const table = document.createElement('table');
    const plus = document.createElement('div');
    const trash = document.createElement('td');
    let dataCount = Object.keys(data).length;
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
    const addTrashFunc = i => {
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

        elems.forEach(prop => {
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

      api.textContent = '';
      api.appendChild(table);
      api.appendChild(plus);
    };

    // init trash functionality
    const initTrash = () => {
      const trashIcons = api.querySelectorAll(`.${classes.trash}`);

      [ ...trashIcons ].forEach(i => {
        addTrashFunc(i);
      });
    };

    // clone last element
    const initClone = () => {
      api.querySelector(`.${classes.plus}`).addEventListener('click', e => {
        e.preventDefault();

        const wrapper = document.createElement('tr');
        const i = trash.cloneNode(true);

        elems.forEach(prop => {
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
      let tableContent = '';

      elems.forEach(prop => {
        tableContent += `<th>${prop.substr(0, 1).toUpperCase()}${prop.substr(1)}</th>`;
      });

      table.innerHTML = `<thead><tr>${tableContent}<th></th></tr></thead>`;
      plus.innerHTML = `<a href="#" class="${classes.plus}"><i class="fa fa-plus-square"></i> Add Row</a>`;
      trash.innerHTML = `<a href="#" class="${classes.trash}"><i class="fa fa-trash"></i></a>`;

      createTable();
      initClone();
      initTrash();
    };

    // INIT
    init();
  }
}, false);
