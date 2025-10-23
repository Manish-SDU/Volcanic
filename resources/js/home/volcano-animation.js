// Volcano Animation Component
class SquigglyFilter extends React.Component {
  render() {
    const { id, i, baseFrequency, numOctaves, type, scale } = this.props;
    
    return (
      React.createElement('filter', { id: `${id}-${i}` },
        React.createElement('feTurbulence', {
          baseFrequency: baseFrequency,
          numOctaves: numOctaves,
          type: type,
          seed: i,
          result: "noise"
        }),
        React.createElement('feDisplacementMap', {
          scale: scale,
          in: "SourceGraphic",
          xChannelSelector: "R",
          yChannelSelector: "G"
        })
      )
    );
  }
}

class Squiggly extends React.Component {
  constructor(props) {
    super(props);

    this.started = false;

    this.state = Object.assign({
      i: 0,
      seed: 0,
    }, this.props);

    this.loop = this.loop.bind(this);
    this.getFilterName = this.getFilterName.bind(this);
  }

  componentDidMount() {
    this.start();
  }

  componentDidUpdate() {
    this.start();
  }

  getFilterName() {
    return `url('#${this.props.id}-${this.state.i}')`;
  }

  start() {
    if (this.props.start) {
      if (!this.started) {
        this.started = true;
        this.loop();
      }
    } else {
      this.started = false;
    }
  }

  loop() {
    if (this.props.start) {
      this.setState({ i: (this.state.i + 1) % 10 });
      setTimeout(this.loop, this.props.freq);
    }
  }

  render() {
    return React.createElement('div', { 
      id: this.props.id, 
      style: { filter: this.getFilterName() } 
    },
      React.createElement('svg', { style: { display: 'none' } },
        React.createElement(SquigglyFilter, { ...this.props })
      ),
      this.props.children
    );
  }
}

class SquigglySVG extends Squiggly {
  render() {
    return React.createElement('g', {
      id: this.props.id,
      style: { filter: this.getFilterName() }
    },
      React.createElement('defs', null,
        React.createElement(SquigglyFilter, { ...this.state })
      ),
      this.props.children
    );
  }
}

class VolcanoApp extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      eruption: true,
    };

    this.toggleLava = this.toggleLava.bind(this);
  }
  
  componentDidMount() {
    // Initial eruption toggle after a delay
    setTimeout(() => {
      this.toggleLava();
    }, 1000);
  }

  toggleLava() {
    const newEruptionState = !this.state.eruption;
    this.setState({ eruption: newEruptionState });
    
    // Toggle the Group 3 text
    const textElement = document.getElementById('group3-text');
    if (textElement) {
      if (newEruptionState) {
        textElement.classList.add('show');
        // Increase squiggle intensity during eruption
        textElement.style.setProperty('--squiggle-scale', '5');
      } else {
        textElement.classList.remove('show');
        // Reset squiggle intensity
        textElement.style.setProperty('--squiggle-scale', '3');
      }
    }
  }
  render() {
    return React.createElement('svg', {
      id: "volcano",
      viewBox: "0 0 200 200",
      onClick: this.toggleLava
    },
      React.createElement('defs', null,
        React.createElement('filter', { id: 'glow' },
          React.createElement('feGaussianBlur', { 
            stdDeviation: '3',
            result: 'coloredBlur'
          }),
          React.createElement('feColorMatrix',
            {
              type: 'matrix',
              values: '1 0 0 0   0 \
                      0 1 0 0   0 \
                      0 0 1 0   0 \
                      0 0 0 15 -7',
              result: 'colorBoost'
            }
          ),
          React.createElement('feMerge', null,
            React.createElement('feMergeNode', { in: 'colorBoost' }),
            React.createElement('feMergeNode', { in: 'SourceGraphic' })
          )
        ),
        React.createElement('filter', { id: 'lava' },
          React.createElement('feTurbulence', {
            type: 'fractalNoise',
            baseFrequency: '0.09',
            numOctaves: '10',
            seed: '1',
            result: 'noise'
          }),
          React.createElement('feDisplacementMap', {
            in: 'SourceGraphic',
            in2: 'noise',
            scale: '6'
          })
        ),
        React.createElement('filter', { id: 'turbulence' },
          React.createElement('feTurbulence', {
            type: 'fractalNoise',
            baseFrequency: '0.09',
            numOctaves: '10',
            seed: '2'
          }),
          React.createElement('feDisplacementMap', {
            in: 'SourceGraphic',
            scale: '4'
          })
        )
      ),
      React.createElement(SquigglySVG, {
        id: "mountain",
        freq: this.state.eruption ? 25 : 100
      },
        React.createElement('path', {
          id: "XMLID_26_",
          className: "st0",
          d: "M36.5,204.2c0,0,35.7-89.7,44-96.6c8.3-6.9,31.1-22.4,51.6,0c13.3,16.1,39.6,96.6,39.6,96.6 H36.5z"
        }),
        React.createElement('path', {
          id: "XMLID_2_",
          className: "st1",
          d: "M86.4,111.9c7.2-15.1,31.9-10.1,31.9-10.1s3.3,5,3.6,11.1s-20.2,7-20.2,7S79.2,127,86.4,111.9z"
        }),
        this.state.eruption
          ? React.createElement('g', { id: "face" },
            React.createElement('ellipse', {
              id: "XMLID_10_",
              className: "st1",
              cx: "104.7",
              cy: "134",
              rx: "1.2",
              ry: "1.8"
            }),
            React.createElement('ellipse', {
              id: "XMLID_8_",
              className: "st1",
              cx: "129.5",
              cy: "131.3",
              rx: "1.4",
              ry: "2.1"
            }),
            React.createElement('g', { id: "mouth" },
              React.createElement('ellipse', {
                id: "XMLID_13_",
                className: "st1",
                cx: "120.5",
                cy: "143",
                rx: "2.6",
                ry: "2.9"
              }),
              React.createElement('path', {
                id: "XMLID_12_",
                className: "st3",
                d: "M117.9,143.5c0.2,1.4,1.3,2.4,2.6,2.4c0.5,0,1-0.2,1.4-0.5c-0.2-1.4-1.3-2.4-2.6-2.4 C118.8,143,118.3,143.2,117.9,143.5z"
              })
            )
          )
          : React.createElement('g', { id: "face2" },
            React.createElement('path', {
              id: "XMLID_21_",
              className: "st4",
              d: "M101.3,134.3c0,0-0.3-3,3.1-3s3.1,3,3.1,3"
            }),
            React.createElement('path', {
              id: "XMLID_22_",
              className: "st4",
              d: "M126.4,133.8c0,0-0.3-3.4,3.1-3.4c3.5,0,3.1,3.4,3.1,3.4"
            }),
            React.createElement('path', {
              id: "XMLID_20_",
              className: "st4",
              d: "M124.9,141c0,0,0.6,5.5-6.5,5.5c-7.1,0-6.5-5.5-6.5-5.5"
            })
          )
      ),
      React.createElement(SquigglySVG, {
        id: "lava",
        scale: 6,
        baseFrequency: 0.09,
        numOctaves: 10,
        type: "fractalNoise",
        start: this.state.eruption
      },
        this.state.eruption
          ? React.createElement('g', null,

            React.createElement('path', {
              id: "XMLID_3_",
              className: "st6",
              d: "M94,113.6c0,0-2.4-12.8-3-22.6C90.3,81.1,85-6,85-6h44.1c0,0-6.7,58.8-9.6,72.2 c-2.9,13.4-3.4,45.6-3.4,45.6L94,113.6z"
            }),
            React.createElement('path', {
              id: "XMLID_4_",
              className: "st7",
              d: "M98.6,114.9c0,0-1.4-83.1-2.7-94.1S91.8-4,91.8-6s29.4,1.1,30.8-0.2c1.4-1.3-3.1,49.6-6.7,61.3 c-3.6,11.7-2.9,58.1-2.9,58.1L98.6,114.9z"
            }),
            React.createElement('path', {
              id: "XMLID_5_",
              className: "st8",
              d: "M104.4,113.6c0,0,1.9-65.1-0.9-75.5c-2.8-10.4-1-44-1-44s10.3,0,12.8,0 c2.5,0-6.3,117.8-6.3,117.8L104.4,113.6z"
            }),
            React.createElement('g', { id: "bubbles" },
              React.createElement('path', {
                id: "XMLID_7_",
                className: "st6",
                d: "M79,53.2c0,1.5-2.8,2.8-2.8,2.8s-2.8-1.2-2.8-2.8s1.2-2.8,2.8-2.8S79,51.7,79,53.2z"
              }),
              React.createElement('circle', {
                id: "XMLID_9_",
                className: "st8",
                cx: "80.6",
                cy: "70.6",
                r: "1.6"
              }),
              React.createElement('circle', {
                id: "XMLID_11_",
                className: "st7",
                cx: "75.4",
                cy: "83.9",
                r: "1.9"
              }),
              React.createElement('path', {
                id: "XMLID_14_",
                className: "st7",
                d: "M62,30.4c0,2.4-1.1,2.4-2.4,2.4s-2.4-1.1-2.4-2.4s1.1-2.4,2.4-2.4S62,28,62,30.4z"
              }),
              React.createElement('ellipse', {
                id: "XMLID_15_",
                className: "st8",
                cx: "133.2",
                cy: "94",
                rx: "2.3",
                ry: "1.7"
              }),
              React.createElement('ellipse', {
                id: "XMLID_16_",
                className: "st6",
                cx: "125.5",
                cy: "79.7",
                rx: "3.6",
                ry: "4.2"
              }),
              React.createElement('circle', {
                id: "XMLID_17_",
                className: "st8",
                cx: "131.4",
                cy: "60",
                r: "2.2"
              }),
              React.createElement('circle', {
                id: "XMLID_18_",
                className: "st7",
                cx: "145.6",
                cy: "54.1",
                r: "1.9"
              }),
              React.createElement('circle', {
                id: "XMLID_19_",
                className: "st6",
                cx: "142.6",
                cy: "32.4",
                r: "2.1"
              })
            )
          )
          : null
      ),
      React.createElement('g', { id: "mask" },
        React.createElement('path', {
          id: "XMLID_6_",
          className: "st0",
          d: "M83.8,109.2c20.4,5.6,40.8-7.4,40.8-7.4s-0.6,20.6-4.6,21.6c-4,1-37.7,5.6-37.7,5.6"
        })
      )
    );
  }
}

// Initialize the volcano component when page is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Check if the volcano container exists, create if not
  let volcanoContainer = document.getElementById('volcano-container');
  if (!volcanoContainer) {
    volcanoContainer = document.createElement('div');
    volcanoContainer.id = 'volcano-container';
    document.body.appendChild(volcanoContainer);
  }
  

  
  // Create and render both volcano and text components
  const textContainer = document.createElement('div');
  textContainer.id = 'text-container';
  document.body.appendChild(textContainer);

  // Render both components
  ReactDOM.render(
    React.createElement(
      React.Fragment,
      null,
      React.createElement(VolcanoApp),
      React.createElement(
        Squiggly,
        {
          id: 'group3-text',
          scale: 2,
          baseFrequency: 0.05,
          numOctaves: 6,
          type: 'fractalNoise',
          start: true,
          freq: 150
        },
        React.createElement('div', { className: 'text-content' }, 'Group 3')
      )
    ),
    volcanoContainer
  );
});