import { createColumnHelper } from "@tanstack/react-table";
import { useQuery } from "@tanstack/react-query";
import { AdminTable } from "@/Components/AdminTable";
import { AdminButton } from "@/Components/AdminButton";

const columnHelper = createColumnHelper();

export default function CompetitionVoteTable() {
    const { data, refetch } = useQuery({
        queryKey: ["competition-votes"],
        queryFn: async () => {
            const res = await fetch("/admin/competition/votes");
            return res.json();
        },
    });

    const columns = [
        columnHelper.accessor("id", { header: "ID" }),

        columnHelper.accessor("entry.id", {
            header: "Nevezés ID",
        }),

        columnHelper.accessor("entry.competition.title", {
            header: "Verseny",
        }),

        columnHelper.accessor("user.name", {
            header: "Szavazó",
        }),

        columnHelper.accessor("created_at", {
            header: "Idopont",
        }),

        columnHelper.display({
            id: "actions",
            header: "Muveletek",
            cell: ({ row }) => (
                <AdminButton
                    variant="danger"
                    onClick={async () => {
                        await fetch(`/admin/competition/votes/${row.original.id}`, {
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
            <h1 className="text-xl font-bold mb-4">Verseny szavazatok</h1>
            <AdminTable data={data || []} columns={columns} />
        </>
    );
}
