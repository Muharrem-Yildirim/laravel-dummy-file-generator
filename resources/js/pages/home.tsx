import { PageProps } from "@/types";
import { useEffect, useState } from "react";
import Echo from "laravel-echo";
import { Button } from "@/components/ui/button";
import axios from "@/lib/axios";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Input } from "@/components/ui/input";
import { Toaster } from "@/components/ui/toaster";
import { useToast } from "@/hooks/use-toast";
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Loader2 } from "lucide-react";
import { Head } from "@inertiajs/react";

export default function Home({ sessionId }: PageProps<{ sessionId: string }>) {
    const [loading, setLoading] = useState(true);
    const [input, setInput] = useState({
        size: 1,
        multiplier: 1024,
    });
    const multipliers = [
        // {
        //     value: 1,
        //     label: "bytes",
        // },
        {
            value: 1024,
            label: "KB",
        },
        {
            value: 1024 * 1024,
            label: "MB",
        },
        {
            value: 1024 * 1024 * 1024,
            label: "GB",
        },
    ];

    useEffect(() => {
        const echo = new Echo({
            broadcaster: "pusher",
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            authEndpoint: "/broadcasting/custom-auth",
        });

        echo.channel(`Session.${sessionId}`)
            .subscribed(() => {
                setLoading(false);
            })
            .listen(".file-generated", (e: any) => {
                console.log(e);
                setLoading(false);

                toast({
                    title: "Success",
                    description: "File generated successfully.",
                });

                window.location.href = e.downloadUrl;
            })
            .listen(".file-could-not-generated", (e: any) => {
                console.log(e);
                setLoading(false);

                toast({
                    title: "Error",
                    description: "File could not generated.",
                    variant: "destructive",
                });
            });

        return () => {
            echo.leave(`Session.${sessionId}`);
        };
    }, [sessionId]);

    const { toast } = useToast();

    return (
        <>
            <Head title="Home" />
            <Toaster />

            <div className="flex justify-center items-center gap-4 w-full min-h-[100vh] h-full">
                <Card className="lg:w-1/3 w-full m-4 lg:m-0 gap-2">
                    <CardHeader>
                        <CardTitle className="text-xl">
                            Generate Dummy File
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div>
                            <Label htmlFor="file_size">File Size</Label>
                            <Input
                                id="file_size"
                                value={input.size}
                                onChange={(e) =>
                                    setInput({
                                        ...input,
                                        size: parseInt(e.target.value),
                                    })
                                }
                                type="number"
                                disabled={loading}
                                placeholder="File size"
                            />
                        </div>
                        <div className="mt-4">
                            <Label>File Unit</Label>
                            <Select
                                value={input.multiplier.toString()}
                                onValueChange={(value) =>
                                    setInput({
                                        ...input,
                                        multiplier: parseInt(value),
                                    })
                                }
                                disabled={loading}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Please select" />
                                </SelectTrigger>
                                <SelectContent>
                                    {multipliers.map((multiplier) => (
                                        <SelectItem
                                            key={multiplier.value}
                                            value={multiplier.value.toString()}
                                        >
                                            {multiplier.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </CardContent>
                    <CardFooter className="justify-end">
                        <Button
                            onClick={() => {
                                setLoading(true);

                                axios
                                    .post(route("generator.store", {}, false), {
                                        file_size:
                                            input.size * input.multiplier,
                                        session_id: sessionId,
                                    })
                                    .catch((e) => {
                                        toast({
                                            title: "Error",
                                            description: e.message,
                                            variant: "destructive",
                                        });
                                        setLoading(false);
                                    });
                            }}
                            disabled={loading}
                        >
                            {loading ? (
                                <Loader2 className="h-4 w-4 animate-spin" />
                            ) : (
                                "Generate File"
                            )}
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </>
    );
}
