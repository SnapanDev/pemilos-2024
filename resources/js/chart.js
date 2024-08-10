import axios from "axios";

Pusher.logToConsole = true;
const pusher = new Pusher('c6904ba2ced6df1a370f', {
    cluster: 'ap1'
});

const osisOptions = {
    series: [
        {
            data: initData.osis.osisVotesCount,
        },
    ],
    title: {
        text: "Live Count Voting OSIS",

        align: "center",
        style: {
            fontSize: "35px",
            color: "#EEEEEE",
        },
    },
    chart: {
        height: '100%',
        type: "bar",
        fontFamily: "Helvetica, Arial, sans-serif",
    },

    tooltip: {
        enabled: false,
    },

    stroke: {
        show: true,
        curve: "bar",
        lineCap: "butt",
        colors: undefined,
        width: 5,
        height: 100,
        dashArray: 0,
    },
    plotOptions: {
        bar: {
            columnWidth: "30%",
            distributed: false,
        },
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: "32px",
        },
    },
    legend: {
        show: true,
    },

    yaxis: {
        labels: {
            style: {
                fontSize: "20px",
                colors: "white",
            },
        },
    },

    xaxis: {
        categories: initData.osis.osisName,
        labels: {
            style: {
                fontSize: "22px",
                colors: "white",
            },
        },
    },
};

const mpkOptions = {
    series: [
        {
            data: initData.mpk.mpkVotesCount,
        },
    ],
    title: {
        text: "Live Count Voting MPK",
        align: "center",
        style: {
            fontSize: "35px",
            color: "#EEEEEE",
        },
    },
    chart: {
        height: '100%',
        type: "bar",
        fontFamily: "Helvetica, Arial, sans-serif",
    },

    tooltip: {
        enabled: false,
    },

    stroke: {
        show: true,
        curve: "bar",
        lineCap: "butt",
        colors: undefined,
        width: 5,
        height: 100,
        dashArray: 0,
    },
    plotOptions: {
        bar: {
            columnWidth: "30%",
            distributed: false,
        },
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: "32px",
        },
    },
    legend: {
        show: false,
    },

    yaxis: {
        labels: {
            style: {
                fontSize: "20px",
                colors: "white",
            },
        },
    },

    xaxis: {
        categories: initData.mpk.mpkName,
        labels: {
            style: {
                fontSize: "22px",
                colors: "white",
            },
        },
    },
};

const osisChart = new ApexCharts(document.querySelector("#chart-osis"), osisOptions)
const mpkChart = new ApexCharts(document.querySelector("#chart-mpk"), mpkOptions)
osisChart.render()
mpkChart.render()

const channel = pusher.subscribe('pemilos');
channel.bind('vote', async function() {
    const data = await updateChart(axios)

    osisChart.updateSeries([
        {
            data: data.osis.data
        }
    ], true)
    mpkChart.updateSeries([
        {
            data: data.mpk.data
        }
    ], true)
});
