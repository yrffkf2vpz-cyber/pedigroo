import { createColumnHelper } from "@tanstack/react-table";
import { useQuery } from "@tanstack/react-query";
import { AdminTable } from "@/Components/AdminTable";
import { AdminButton } from "@/Components/AdminButton";

const columnHelper = createColumnHelper();

export default function CompetitionEntryTable() {
    const { data, refetch } = useQuery({
        queryKey: ["competition-entries"],
        queryFn: async () => {
            const res = await fetch("/admin/competition/entries");
            return res.json();
        },
    });

    const columns = [
        columnHelper.accessor("id", { header: "ID" }),

        columnHelper.accessor("competition.title", {
            header: "Verseny",
        }),

        columnHelper.accessor("user.name", {
            header: "Felhasználó",
        }),

        columnHelper.accessor("media_url", {
            header: "Média",
            cell: (info) => {
                const url = info.getValue();
                const type = info.row.original.media_type;

                if (type === "image") {
                    return (
                        <img
                            src={url}
                            alt="entry"
                            className="w-20 h-20 object-cover rounded"
                        />
                    );
                }

                return (
                    <video
                        src={url}
                        className="w-20 h-20 rounded"
                        muted
                        controls={false}
                    />
                );
            },
        }),

        columnHelper.accessor("votes_count", {
            header: "Szavazatok",
        }),

        columnHelper.display({
            id: "actions",
            header: "Muveletek",
            cell: ({ row }) => (
                <AdminButton
                    variant="danger"
                    onClick={async () => {
                        await fetch(`/admin/competition/entries/${row.original.id}`, {
                            method: "DELETE",
                        });
                        refetch();
                    }}
                >
                    Törlés
                </AdminButton>
            ),
        }),
    ];

    return (
        <>
            <h1 className="text-xl font-bold mb-4">Verseny nevezések</h1>
            <AdminTable data={data || []} columns={columns} />
        </>
    );
}
