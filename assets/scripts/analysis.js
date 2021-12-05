/*EXT JS File For Analytics.php*/
class Charter {
    constructor() {
        this.labels = [
            [ '2015', '2016', '2017', '2018' ],
            [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
            [ 'PLU#4046', 'PLU#4225', 'PLU#4770', 'Uncategorized' ],
            [ 'Small Bags', 'Large Bags', 'XL Bags', 'Uncategorized' ]
        ];
        this.data = [];
        this.config = [];
        this.chart = [];
    }                       
    updateCharts(volumePLUs, volumeBags, piePLUs, pieBags, ctrl) {
        // 0 - Main, Line Chart (PLU Volume Over Time)
        var labels = this.labels[1]; var entries = 12; var uncategorized = []; 
        if ( ctrl == 0 ) { labels = this.labels[0]; entries = 4;}
        for (var i = 0; i < entries; i++) { 
            var val = ( volumePLUs['TotalVolume'][i] - ( volumePLUs['TotalPLU4046'][i] + volumePLUs['TotalPLU4225'][i] + volumePLUs['TotalPLU4770'][i] ) );
            if (val < 0 ) {uncategorized.push( 0 ); } else { uncategorized.push( val ); }
        }
        this.data.push({
            labels: labels,
            datasets: [{
                label: 'PLU#4046',
                backgroundColor: '#4a7337',
                borderColor: '#4a7337',
                data: volumePLUs['TotalPLU4046'],
            }, {
                label: 'PLU#4225',
                backgroundColor: '#6b8c21',
                borderColor: '#6b8c21',
                data: volumePLUs['TotalPLU4225'],
            }, {
                label: 'PLU#4770',
                backgroundColor: '#cda989',
                borderColor: '#cda989',
                data: volumePLUs['TotalPLU4770'],
            }, {
                label: 'Uncategorized',
                backgroundColor: '#ddd48f',
                borderColor: '#ddd48f',
                data: uncategorized,
            }]
        });
        this.config.push( { type: 'line', data: this.data[0], options: {} } );
        
        // 1 - Pie Chart 1: % Makeup of PLUs/Total
        var uncategorized = piePLUs[0] - ( piePLUs[1] + piePLUs[2] + piePLUs[3] );
        this.data.push({
            labels: this.labels[2],
            datasets: [{
                backgroundColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f'],
                borderColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f'],
                data: [ piePLUs[1], piePLUs[2], piePLUs[3], uncategorized ]
            }]
        });
        this.config.push( { type: 'pie', data: this.data[1], options: {} } );
        
        // 2 - Pie Chart 2: % Makeup of Bags/Total
        var uncategorized = pieBags[0] - ( pieBags[1] + pieBags[2] + pieBags[3] );
        this.data.push({
            labels: this.labels[3],
            datasets: [{
                backgroundColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f'],
                borderColor: ['#4a7337', '#6b8c21', '#cda989', '#ddd48f'],
                data: [ pieBags[1], pieBags[2], pieBags[3], uncategorized ]
            }]
        });
        this.config.push( { type: 'pie', data: this.data[2], options: {} } );
        
        //OUTPUT: Create Charts
        for ( var i = 0; i < 3; i++) {
            $("canvas#chart" + i).remove();
            $("#cc" + i).append('<canvas id="chart' + i + '"></canvas>');
            if ( typeof this.chart[i] !== 'undefined' ) { this.chart[i].destroy(); }
            this.chart.push( new Chart( document.getElementById( 'chart' + i ), this.config[i] ) );
        }
    }
}
