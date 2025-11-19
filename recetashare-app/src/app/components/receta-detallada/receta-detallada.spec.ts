import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecetaDetallada } from './receta-detallada';

describe('RecetaDetallada', () => {
  let component: RecetaDetallada;
  let fixture: ComponentFixture<RecetaDetallada>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RecetaDetallada]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RecetaDetallada);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
