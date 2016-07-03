import React from 'react';
import Dropzone from 'dropzone';
import {Config} from '../Utils/Constants';
import Utils from '../Utils/Utils';
import ApiUtils from '../Api/ApiUtils';
import AccessToken from '../Api/AccessToken';

// Create dropzone instances ourselves:
Dropzone.autoDiscover = false;

export default class SRDropzone extends React.Component {
  static propTypes = {
    style: React.PropTypes.object,
    activeStyle: React.PropTypes.object,
    multiple: React.PropTypes.bool,
    onUploadStart: React.PropTypes.func,
    onUploadEnd: React.PropTypes.func,
    onUploadSuccess: React.PropTypes.func,
  };
  static defaultProps = {
    style: {},
    activeStyle: {},
    multiple: true,
    onUploadStart: () => {},
    onUploadEnd: () => {},
    onUploadSuccess: () => {},
  };

  constructor(props) {
    super(props);

    this.dropzone = null;

    this.state = {};
  }

  cleanDropzone() {
    this.dropzone.removeAllFiles();
  }

  componentDidMount() {
    let self = this;
    // AccessToken.get()
    //   .then(token => {
        let dzOptions = {
          // url: ApiUtils.buildUrl('/upload-file?token='+token),
          url: ApiUtils.buildUrl('/archives/upload-file'),
          dictDefaultMessage: "<h3>Drop Image Here</h3><p> or Click to Upload</p>",
          init: function() {

            this.on("success", (file, response) => {
              // console.log("success", file, response);
              self.props.onUploadSuccess(response.data.file_uri);
            });

            this.on("addedfile", file => {
              self.props.onUploadStart();
              // Append baseUrl to the file preview element.
              let dzName = file.previewElement.querySelector('[data-dz-name]')
              dzName.innerHTML = Config.uploads_dir + file.name;
            });

            this.on("error", (file, err) => {
              Utils.showError(err);
            });

            this.on("complete", file => {
              self.props.onUploadEnd();
            });

          },
        };
        this.dropzone = new Dropzone(this.refs.dropzone, dzOptions);
      // });
  }

  render() {
    return (
      <div ref="dropzone" style={this.props.style} className="dropzone">
        {this.props.children}
      </div>
    );
  }
}