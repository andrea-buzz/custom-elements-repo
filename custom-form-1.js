class CustomForm1 extends HTMLElement {
    constructor() {
        // Inititialize custom component
        super();
        //this.internals = this.attachInternals();
        this.attachShadow({ mode: 'open' });
        const template = document.createElement('template');
        template.innerHTML = `<style>
        :host {
            display: block;
            border: 1px solid #000;
            background-image: linear-gradient(90deg, #fafafa, #f5f5f5);
        }
        form {
                display: flex;
                flex-direction: column;
                flex-wrap: wrap;
                max-width: 100%;
                row-gap: 1rem;
                /*width: calc(100vw - 120px);*/
                padding: 30px;
                margin: 0;
                
                align-items: stretch;
            }
            form > div {
                
                display: flex;
                flex-direction: column;
                flex-wrap: wrap;
                
                row-gap: .3rem;
            }
            form > div > label {
                font-size: 15px;
                font-weight: bold;
                white-space: nowrap;
                
            }
            
            form > div > input[type="text"], 
            form > div > input[type="email"],
            form > div > input[type="date"]{
                font-size: 18px;
                padding: 8px;
            }
            form > div > input[type="date"]{
                display: block;
                color: #333;
            }
            form > div > .error {
                font-size: 14px;
                color: red;
            }
            form > div > button {
                font-size: 18px;
                padding: 8px 16px;
            }
            form > div > select {
                font-size: 18px;
                padding: 8px;
            }
            form > div > textarea {
                font-size: 18px;
                padding: 8px;
            }
            @media screen and (min-width:1024px){
                form {
                    display: grid;
                    max-width: 100%;
                    row-gap: 1rem;
                    width: 1024px;
                    padding: 30px;
                    margin: 30px;
                    /*border: 1px solid #000;
                    background-image: linear-gradient(90deg, #fafafa, #f5f5f5);*/
                    grid-template-columns: repeat(3, 4fr);
                    grid-column-gap: 3%;
                }
                form > div {
                    flex-basis: 100%;
                    column-gap: 5%;
                    grid-column: span 1;
                }
                form select[multiple] > option {
                    display: block;
                }
                form select[multiple] > option:before {
                    contain:'x';
                }
            }
            </style>
        <form action="" method="" enctype=""><div><label>seleziona un file</label><input type="file" name="attachment1" /></div>
            <div><button type="submit" name="submit">Start Upload</button></div></form>`;
        this.shadowRoot.appendChild(template.content.cloneNode(true));
    
    }
    static get observedAttributes() {
        return ['action', 'enctype', 'method', 'data-fields', 'data-values'];
    }
    attributeChangedCallback(name, oldValue, newValue) {
        switch(name){
            case 'action':
                this.select('form').action = newValue;
                break;
            case 'method':
                this.select('form').method = newValue;
                break;
            case 'enctype':
                this.select('form').enctype = newValue;
                break;
            case 'data-fields':
                this.buildForm(newValue);
                break;

        }
    }
    connectedCallback() {
    
    }
    get select() {
        return this.shadowRoot.querySelector.bind(this.shadowRoot);
    }
    buildForm(data){
        const fields = JSON.parse(data);
        this.select('form').innerHTML = '';
        const t = this.select('form')
        fields.forEach((f) => {
            let input_id = 'f_';
            let err = '';
            switch(f.tagname){
                case 'input':
                    if('hidden' == f.type){
                        t.innerHTML += `<input type="${f.type}" name="${f.name}" value="${f.value}" />`
                    }else if('file' == f.type){
                        input_id = 'f_' + f.type + '_'  + f.name; 
                        err = f.valid?'':`<div class="error">${f.error}</div>`;
                        t.innerHTML += `<div><label for="${input_id}">${f.label}</label><button type="button" onclick="this.parentElement.querySelector('input[type=file]').click()">${f.label}</button><input type="${f.type}" name="${f.name}" value="${f.value}" id="${input_id}" hidden />${err}</div>`;
                    }else{
                        input_id = 'f_' + f.type + '_'  + f.name; 
                        err = f.valid?'':`<div class="error">${f.error}</div>`;
                        let attr_value = '';
                        if(f.value){
                            attr_value = ` value="${f.value}" `;
                        }
                        t.innerHTML += `<div><label for="${input_id}">${f.label}</label><input type="${f.type}" name="${f.name}" id="${input_id}" ${f.placeholder?`placeholder="${f.placeholder}"`:''} ${attr_value} />${err}</div>`;  //   ${f.required? 'required':''}
                    }
                    break;
                case 'textarea':
                    input_id = 'f_' + f.tagname + '_'  + f.name; 
                    err = f.valid?'':`<div class="error">${f.error}</div>`;
                    t.innerHTML += `<div><label for="${input_id}">${f.label}</label><${f.tagname} name="${f.name}" id="${input_id}" rows="3">${f.value}</${f.tagname}>${err}</div>`;
                    break;
                case 'select':
                    input_id = 'f_' + f.tagname + '_'  + f.name; 
                    err = f.valid?'':`<div class="error">${f.error}</div>`;
                    t.innerHTML += `<div><label for="${input_id}">${f.label}</label><${f.tagname} name="${f.name}" id="${input_id}" ${f.multiple?' multiple':''}>${this.render_select_options(f.options, f.value)}</${f.tagname}>${err}</div>`;
                    break;
            }
            
        });
        t.innerHTML += `<div><button type="submit" name="submit">Start Upload</button></div>`;
        console.log(fields);
    }
    render_select_options(options, value){
        let html = '';
        let isSelected;
        options.forEach((o)=>{
            if( 'string' === typeof value && value.length){
                isSelected = value===o.value;
            }
            if( Array.isArray(value) && value.length){
                isSelected = value.includes(o.value);
            }
            
            html += `<option value="${o.value}" ${isSelected?' selected':''}>${o.text}</option>`;    
        });
        return html;
    }
}
window.customElements.define('custom-form-1', CustomForm1 );