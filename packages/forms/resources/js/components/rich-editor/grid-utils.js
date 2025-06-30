export function createGrid(schema, colsCount, stackAt, asymmetric, leftSpan = null, rightSpan = null, colContent = null) {
    const { grid, column } = getGridNodeTypes(schema);
    const cols = [];
    let type = asymmetric === false ? 'symmetric' : 'asymmetric';

    if (asymmetric) {
        cols.push(createColumn(column, leftSpan, colContent));
        cols.push(createColumn(column, rightSpan, colContent));
    } else {
        for (let index = 0; index < colsCount; index += 1) {
            const col = createColumn(column, 1, colContent);

            if (col) {
                cols.push(col);
            }
        }
    }

    return grid.createChecked({
        'data-columns': colsCount,
        'data-type': type,
        'data-stack-at': stackAt
    }, cols);
}

export function getGridNodeTypes(schema) {
    if (schema.cached.gridNodeTypes) {
        return schema.cached.gridNodeTypes;
    }

    const roles = {};

    Object.keys(schema.nodes).forEach((type) => {
        const nodeType = schema.nodes[type];

        if (nodeType.spec.gridRole) {
            roles[nodeType.spec.gridRole] = nodeType;
        }
    });

    schema.cached.gridNodeTypes = roles;

    return roles;
}

export function createColumn(colType, colSpan, colContent = null) {
    if (colContent) {
        return colType.createChecked({'data-col-span': colSpan}, colContent);
    }

    return colType.createAndFill({'data-col-span': colSpan});
}
