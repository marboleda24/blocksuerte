export const tailwindTheme = () => {
    return {
        framework: 'tailwind',
        table: 'table shadow-lg overflow-hidden border-b border-gray-400 rounded-lg table-sm',
        thead: 'table-dark',
        th: 'border px-4 py-2',
        td: 'border px-4 py-2',
        tr: '',
        trEven: '',
        trOdd: '',
        row: 'grid-rows-1',
        column: 'flex',
        label: 'label',
        input: 'p-1 border',
        select: 'p-1 border',
        field: 'flex-initial m-2',
        inline: 'is-horizontal',
        right: 'is-pulled-right',
        left: 'is-pulled-left',
        center: 'text-center mt-3',
        contentCenter: 'justify-center',
        icon: 'icon',
        small: 'is-small',
        nomargin: 'marginless',
        button: 'button',
        groupTr: 'is-selected',
        dropdown: {
            container: 'dropdown flex-initial m-2 relative',
            trigger: 'dropdown-trigger border round p-1',
            menu: 'dropdown-menu absolute z-50 bg-white border p-2',
            content: 'dropdown-content truncate flex-1',
            item: 'dropdown-item mb-1 flex',
            caret: 'fa fa-angle-down',
            checkbox: 'mt-1',
            text: 'text-left ml-1'
        },
        pagination: {
            nav: 'text-center pt-2',
            count: 'text-center flex-row',
            wrapper: 'pagination',
            list: 'flex flex-row',
            item: 'btn',
            link: '',
            next: '',
            prev: '',
            active: 'btn-primary',
            disabled: 'btn btn-secondary'
        }
    }
};

export default tailwindTheme;
