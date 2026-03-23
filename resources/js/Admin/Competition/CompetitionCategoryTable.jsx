import { createColumnHelper } from "@tanstack/react-table";
import { useQuery } from "@tanstack/react-query";
import { AdminTable } from "@/Components/AdminTable";
import { AdminButton } from "@/Components/AdminButton";
import { AdminFormModal } from "@/Components/AdminFormModal";
import { useState } from "react";

const columnHelper = createColumnHelper();

export default function CompetitionCategoryTable() {
    const [open, setOpen] = useState(false);

    const { data, refetch } = useQuery({
        queryKey: ["competition-categories"],
        queryFn: async () => {
            const res = await fetch("/admin/competition/categories");
            return res.json();
        },
    });

    const columns = [
        columnHelper.accessor("name", {
            header: "Név",
        }),
        columnHelper.accessor("slug", {
            header: "Slug",
        }),
        columnHelper.accessor("media_type", {
            header: "Média típus",
        }),
        columnHelper.accessor("category_type", {
            header: "Kategória típus",
        }),
        columnHelper.accessor("generate_frequency", {
            header: "Generálási gyakoriság",
        }),
        columnHelper.accessor("ai_weight", {
            header: "AI súly",
        }),
        columnHelper.accessor("is_active", {
            header: "Aktív?",
            cell: (info) => (info.getValue() ? "Igen" : "Nem"),
        }),
    ];

    return (
        <>
            <div className="flex justify-between mb-4">
                <h1 className="text-xl font-bold">Versenykategóriák</h1>
                <AdminButton onClick={() => setOpen(true)}>Új kategória</AdminButton>
            </div>

            <AdminTable data={data || []} columns={columns} />

            <AdminFormModal
                open={open}
                onClose={() => setOpen(false)}
                title="Új versenykategória"
                onSubmit={async (formData) => {
                    await fetch("/admin/competition/categories", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(formData),
                    });
                    setOpen(false);
                    refetch();
                }}
                fields={[
                    { name: "name", label: "Név", type: "text", required: true },
                    { name: "description", label: "Leírás", type: "textarea" },
                    {
                        name: "media_type",
                        label: "Média típus",
                        type: "select",
                        options: [
                            { value: "image", label: "Kép" },
                            { value: "video", label: "Videó" },
                            { value: "both", label: "Mindketto" },
                        ],
                    },
                    {
                        name: "category_type",
                        label: "Kategória típus",
                        type: "select",
                        options: [
                            { value: "beauty", label: "Szépség" },
                            { value: "funny", label: "Vicces" },
                            { value: "sport", label: "Sport" },
                            { value: "working", label: "Munkakutya" },
                            { value: "hunting", label: "Vadász" },
                            { value: "show", label: "Kiállítás" },
                            { value: "costume", label: "Jelmez" },
                            { value: "trending", label: "Trending" },
                            { value: "custom", label: "Egyedi" },
                        ],
                    },
                    {
                        name: "generate_frequency",
                        label: "Generálási gyakoriság",
                        type: "select",
                        options: [
                            { value: "none", label: "Nincs" },
                            { value: "daily", label: "Napi" },
                            { value: "weekly", label: "Heti" },
                            { value: "monthly", label: "Havi" },
                            { value: "seasonal", label: "Évszakos" },
                            { value: "yearly", label: "Éves" },
                            { value: "trending", label: "Trending" },
                        ],
                    },
                    { name: "ai_weight", label: "AI súly", type: "number", defaultValue: 1 },
                    { name: "is_active", label: "Aktív?", type: "checkbox", defaultValue: true },
                    { name: "auto_generate", label: "Automatikus generálás?", type: "checkbox" },
                ]}
            />
        </>
    );
}
