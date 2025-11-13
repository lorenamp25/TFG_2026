import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecetaTabla } from './receta-tabla';

describe('RecetaTabla', () => {
  let component: RecetaTabla;
  let fixture: ComponentFixture<RecetaTabla>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RecetaTabla]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RecetaTabla);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
