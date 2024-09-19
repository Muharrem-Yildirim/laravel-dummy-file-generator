import { PageProps } from "@/types";
import { useEffect, useState } from "react";
import Echo from "laravel-echo";
import { Button } from "@/components/ui/button";
import axios from "@/lib/axios";

export default function Home({ sessionId }: PageProps<{ sessionId: string }>) {
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const echo = new Echo({
            broadcaster: "pusher",
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            authEndpoint: "/broadcasting/custom-auth",
        });

        echo.channel(`Session.${sessionId}`)
            .listen(".file-generated", (e: any) => {
                console.log(e);
                setLoading(false);

                window.location.href = e.downloadUrl;
            })
            .listen(".file-could-not-generated", (e: any) => {
                console.log(e);
                setLoading(false);
            });

        return () => {
            echo.leave(`Session.${sessionId}`);
        };
    }, [sessionId]);

    return (
        <>
            <Button
                onClick={() => {
                    setLoading(true);
                    axios
                        .post(
                            route("generator.store", {
                                file_size: 1024 * 1024 * 1000,
                                session_id: sessionId,
                            })
                        )
                        .catch((e) => {
                            setLoading(false);
                        });
                }}
                disabled={loading}
            >
                Generate
            </Button>
        </>
    );
}
