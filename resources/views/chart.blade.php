<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart OSIS</title>
    @vite('resources/css/app.css')
    @vite('resource/js/app.js')
</head>
<script>
    const initData = {
        osis: {
            osisName: {!! json_encode($osis->pluck('name')) !!},
            osisVotesCount: {!! json_encode($osis->pluck('votes_count')) !!}
        },
        mpk: {
            mpkName: {!! json_encode($mpk->pluck('name')) !!},
            mpkVotesCount: {!! json_encode($mpk->pluck('votes_count')) !!}
        }
    }

    const updateChart = async (fetcher) => {
        const {data} = await fetcher.get('{{ route('api.live-count') }}')
        return data
    }
</script>
<body class="bg-[#17153b] h-screen p-4 md:p-16">
    <div id="chart-{{ $label }}" class="mx-auto"></div>
</body>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@vite('resources/js/chart.js')
</html>
