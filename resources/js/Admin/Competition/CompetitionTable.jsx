import { createColumnHelper } from "@tanstack/react-table";
import { useQuery } from "@tanstack/react-query";
import { AdminTable } from "@/Components/AdminTable";
import { AdminButton } from "@/Components/AdminButton";
import { AdminFormModal } from "@/Components/AdminFormModal";
import { useState } from "react";

const columnHelper = createColumnHelper();

export default function CompetitionTable() {
    const [open, setOpen] = useState(false);

    const { data, refetch } = useQuery({
        queryKey: ["competitions"],
        queryFn: async () => {
            const res = await fetch("/admin/competitions");
            return res.json();
        },
    });

    const columns = [
        columnHelper.accessor("title", { header: "Cím" }),
        columnHelper.accessor("category.name", { header: "Kategória" }),
        columnHelper.accessor("status", { header: "Státusz" }),
        columnHelper.accessor("starts_at", { header: "Indul" }),
        columnHelper.accessor("ends_at", { header: "Vége" }),
        columnHelper.accessor("is_auto_generated", {
            header: "AI generált?",
            cell: (info) => (info.getValue() ? "Igen" : "Nem"),
        }),
        columnHelper.display({
            id: "actions",
            header: "Muveletek",
            cell: ({ row }) => (
                <AdminButton
                    onClick={async () => {
                        await fetch(`/admin/competitions/${row.original.id}/finish`, {
                            method: "POST",
                        });
                        refetch();
                    }}
                >
                    Lezárás
                </AdminButton>
            ),
        }),
    ];

    return (
        <>
            <div className="flex justify-between mb-4">
                <h1 className="text-xl font-bold">Versenyek</h1>
                <AdminButton onClick={() => setOpen(true)}>Új verseny</AdminButton>
            </div>

            <AdminTable data={data || []} columns={columns} />

            <AdminFormModal
                open={open}
                onClose={() => setOpen(false)}
                title="Új verseny"
                onSubmit={async (formData) => {
                    await fetch("/admin/competitions", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(formData),
                    });
                    setOpen(false);
                    refetch();
                }}
                fields={[
                    {
                        name: "category_id",
                        label: "Kategória",
                        type: "select",
                        asyncOptions: async () => {
                            const res = await fetch("/admin/competition/categories");
                            const categories = await res.json();
                            return categories.map((c) => ({
                                value: c.id,
                                label: c.name,
                            }));
                        },
                    },
                    { name: "title", label: "Cím", type: "text", required: true },
                    { name: "description", label: "Leírás", type: "textarea" },
                    { name: "starts_at", label: "Indul", type: "datetime-local", required: true },
                    { name: "ends_at", label: "Vége", type: "datetime-local", required: true },
                ]}
            />
        </>
    );
}
