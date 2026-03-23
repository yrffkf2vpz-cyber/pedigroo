import React, { useEffect, useState } from "react";
import {
    useReactTable,
    getCoreRowModel,
    getSortedRowModel,
    flexRender,
} from "@tanstack/react-table";

export default function BuyerAccessList() {
    const [data, setData] = useState([]);
    const [sorting, setSorting] = useState([]);

    useEffect(() => {
        fetch("/api/admin/buyer-access")
            .then((res) => res.json())
            .then((json) => setData(json.data));
    }, []);

    const columns = [
        {
            accessorKey: "id",
            header: "ID",
        },
        {
            accessorKey: "buyer.name",
            header: "Buyer",
            cell: ({ row }) => (
                <div>
                    {row.original.buyer.name}
                    <div className="text-muted small">#{row.original.buyer.id}</div>
                </div>
            ),
        },
        {
            accessorKey: "dog.name",
            header: "Dog",
            cell: ({ row }) => (
                <div>
                    {row.original.dog.name}
                    <div className="text-muted small">#{row.original.dog.id}</div>
                </div>
            ),
        },
        {
            accessorKey: "kennel.name",
            header: "Kennel",
            cell: ({ row }) => (
                <div>
                    {row.original.kennel.name}
                    <div className="text-muted small">#{row.original.kennel.id}</div>
                </div>
            ),
        },
        {
            accessorKey: "purpose",
            header: "Purpose",
        },
        {
            accessorKey: "status",
            header: "Status",
            cell: ({ row }) => {
                const s = row.original.status;
                const color =
                    s === "pending"
                        ? "warning"
                        : s === "approved"
                        ? "success"
                        : "danger";

                return <span className={`badge bg-${color}`}>{s}</span>;
            },
        },
        {
            accessorKey: "created_at",
            header: "Requested At",
        },
        {
            id: "actions",
            header: "",
            cell: ({ row }) => (
                <a
                    href={`/admin/buyer-access/${row.original.id}`}
                    className="btn btn-sm btn-primary"
                >
                    View
                </a>
            ),
        },
    ];

    const table = useReactTable({
        data,
        columns,
        state: { sorting },
        onSortingChange: setSorting,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
    });

    return (
        <div className="container-fluid">
            <h1 className="mb-4">Buyer Access Requests</h1>

            <table className="table table-striped">
                <thead>
                    {table.getHeaderGroups().map((headerGroup) => (
                        <tr key={headerGroup.id}>
                            {headerGroup.headers.map((header) => (
                                <th
                                    key={header.id}
                                    onClick={header.column.getToggleSortingHandler()}
                                    style={{ cursor: "pointer" }}
                                >
                                    {flexRender(
                                        header.column.columnDef.header,
                                        header.getContext()
                                    )}
                                </th>
                            ))}
                        </tr>
                    ))}
                </thead>

                <tbody>
                    {table.getRowModel().rows.map((row) => (
                        <tr key={row.id}>
                            {row.getVisibleCells().map((cell) => (
                                <td key={cell.id}>
                                    {flexRender(
                                        cell.column.columnDef.cell,
                                        cell.getContext()
                                    )}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}